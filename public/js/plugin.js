class SocialDataCore {

    constructor() {

        if (String.prototype.format) {
            return;
        }

        String.prototype.format = function () {

            let args = arguments;

            return this.replace(/{(\d+)}/g, function (match, number) {
                return typeof args[number] != 'undefined'
                    ? args[number]
                    : match
                    ;
            });
        };
    }

    init() {

        let subMenu = [],
            user = pimcore.globalmanager.get('user');

        if (user.isAllowed('social_data_bundle_menu_settings')) {
            subMenu.push(new Ext.Action({
                id: 'social_data_bundle_setting_button',
                text: t('social_data.settings.configuration'),
                iconCls: 'social_data_icon_config',
                handler: this.openSettingsPanel.bind(this)
            }));
        }

        if (user.isAllowed('social_data_bundle_menu_walls')) {
            subMenu.push(new Ext.Action({
                id: 'social_data_bundle_walls_button',
                text: t('social_data.settings.walls'),
                iconCls: 'social_data_icon_walls',
                handler: this.openWallsPanel.bind(this)
            }));
        }

        if (layoutToolbar.settingsMenu && subMenu.length > 0) {
            layoutToolbar.settingsMenu.add({
                text: t('social_data.settings.menu'),
                iconCls: 'social_data_icon_bundle',
                hideOnClick: false,
                menu: {
                    cls: 'pimcore_navigation_flyout',
                    shadow: false,
                    items: subMenu
                }
            });
        }
    }

    openSettingsPanel() {
        try {
            pimcore.globalmanager.get('social_data_bundle_settings').activate();
        } catch (e) {
            pimcore.globalmanager.add('social_data_bundle_settings', new SocialData.SettingsPanel());
        }
    }

    openWallsPanel() {
        try {
            pimcore.globalmanager.get('social_data_bundle_walls').activate();
        } catch (e) {
            pimcore.globalmanager.add('social_data_bundle_walls', new SocialData.WallsPanel());
        }
    }
}

const socialDataCoreHandler = new SocialDataCore();

document.addEventListener(pimcore.events.pimcoreReady, socialDataCoreHandler.init.bind(socialDataCoreHandler));
