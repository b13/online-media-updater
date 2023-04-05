/**
 * Module: TYPO3/CMS/OnlineMediaUpdater/Updater
 * @todo: Remove when v11 support was dropped
 *
 * Handle hreflang validation
 * @exports TYPO3/CMS/OnlineMediaUpdater/Updater
 */

define([
  'TYPO3/CMS/Core/Ajax/AjaxRequest',
  'TYPO3/CMS/Backend/Notification',
  'nprogress',
], (AjaxRequest, Notification, nprogress) => {
  'use strict';

  class Updater {
    constructor() {
      document.querySelectorAll('.t3js-filelist-update-metadata').forEach((item) => {
        item.addEventListener('click', (event) => {
          this.update(event);
        })
      })
    }

    update(event) {
      const url = TYPO3.settings.ajaxUrls.b13_online_media_updater;
      const filename = event.currentTarget.dataset.filename
      const payload = {
        uid: event.currentTarget.dataset.fileUid
      }

      nprogress.start();
      new AjaxRequest(url)
          .post(payload).then(async function (response) {
        const data = await response.resolve();

        Notification.success(
            TYPO3.lang['online_media_updater.alert.success'],
            TYPO3.lang['online_media_updater.alert.success.text'] + ' ' + filename
        );
        document.location.reload();
      }, function (error) {
        Notification.error(TYPO3.lang['online_media_updater.alert.error'], error.response.status + ' ' + error.response.statusText);
      });
    }
  }

  return new Updater();
});
