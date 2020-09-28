pimcore.registerNS('SocialData.Feed.Facebook');
SocialData.Feed.Facebook = Class.create(SocialData.Feed.AbstractFeed, {

    panel: null,

    getLayout: function () {

        this.panel = new Ext.form.FormPanel({
            title: false,
            defaults: {
                labelWidth: 200
            },
            items: this.getConfigFields()
        });

        return this.panel;
    },

    getConfigFields: function () {

        var fields = [];

        fields.push(
            {
                xtype: 'textfield',
                value: this.data !== null ? this.data['pageId'] : null,
                fieldLabel: t('social_data.wall.feed.facebook.page_id'),
                name: 'pageId',
                labelAlign: 'left',
                anchor: '100%',
                flex: 1
            },
            {
                xtype: 'numberfield',
                value: this.data !== null ? this.data['limit'] : null,
                fieldLabel: t('social_data.wall.feed.facebook.limit'),
                name: 'limit',
                labelAlign: 'left',
                anchor: '100%',
                flex: 1
            }
        );

        return fields;
    },

    isValid: function () {
        return this.panel.form.isValid();
    },

    getValues: function () {
        return this.panel.form.getValues();
    }
});