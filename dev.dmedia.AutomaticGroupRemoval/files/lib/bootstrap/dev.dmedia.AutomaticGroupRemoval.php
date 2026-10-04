<?php

use wcf\acp\form\UserGroupRemovalAddForm;
use wcf\acp\page\UserGroupRemovalListPage;
use wcf\event\acp\menu\item\ItemCollecting;
use wcf\system\event\EventHandler;
use wcf\system\menu\acp\AcpMenuItem;
use wcf\system\request\LinkHandler;
use wcf\system\style\FontAwesomeIcon;
use wcf\system\WCF;

return static function (): void {
    $eventHandler = EventHandler::getInstance();

    $eventHandler->register(
        ItemCollecting::class,
        static function (ItemCollecting $event): void {
            if (!WCF::getSession()->getPermission('admin.user.canManageGroupAssignment')) {
                return;
            }

            $event->register(new AcpMenuItem(
                'wcf.acp.menu.link.group.removal',
                parentMenuItem: 'wcf.acp.menu.link.group',
                link: LinkHandler::getInstance()->getControllerLink(UserGroupRemovalListPage::class),
            ));

            $event->register(new AcpMenuItem(
                'wcf.acp.menu.link.group.removal.add',
                parentMenuItem: 'wcf.acp.menu.link.group.removal',
                link: LinkHandler::getInstance()->getControllerLink(UserGroupRemovalAddForm::class),
                icon: FontAwesomeIcon::fromValues('plus')
            ));
        }
    );
};
