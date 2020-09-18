pimcore.registerNS('SocialData.Connector.Facebook');
SocialData.Connector.Facebook = Class.create(SocialData.Connector.AbstractConnector, {

    hasCustomConfiguration: function () {
        return true;
    },

    connectHandler: function (stateType, mainBtn) {

        var stateData = this.states[stateType],
            flag = this.data[stateData.identifier] === true ? 'deactivate' : 'activate';

        // just go by default
        if (flag === 'deactivate') {
            this.stateHandler(stateType, mainBtn);
            return;
        }

        mainBtn.setDisabled(true);

        var win = new Ext.Window({
            width: 400,
            bodyStyle: 'padding:10px',
            title: t('social_data.connector.facebook.connect_service'),
            html: t('social_data.connector.facebook.connect_service_note'),
            listeners: {
                beforeclose: function () {
                    mainBtn.setDisabled(false);
                }
            },
            buttons: [{
                text: t('social_data.connector.facebook.connect'),
                iconCls: 'pimcore_icon_open_window',
                handler: function (btn) {
                    var buttons = btn.up('window').query('button');
                    buttons[1].setDisabled(false);
                    btn.setDisabled(true);
                    // use http://localhost:2332 or something in dev context
                    window.open('/admin/social-data/connector/facebook/connect', '_blank');
                }
            }, {
                text: t('social_data.connector.facebook.check_and_apply'),
                iconCls: 'pimcore_icon_apply',
                disabled: true,
                handler: function () {
                    win.close();
                    this.stateHandler('connection', mainBtn);
                }.bind(this)
            }]
        });

        win.show();
    },

    getCustomConfigurationFields: function (data) {

        return [
            {
                xtype: 'textfield',
                name: 'appId',
                fieldLabel: 'App ID',
                allowBlank: false,
                value: data.hasOwnProperty('appId') ? data.appId : null
            },
            {
                xtype: 'textfield',
                name: 'appSecret',
                fieldLabel: 'App Secret',
                allowBlank: false,
                value: data.hasOwnProperty('appSecret') ? data.appSecret : null
            }
        ];
    }
});