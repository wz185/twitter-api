Ext.define('UserActivityHistogram.view.main.SearchFilter', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.search-filter',

    requires: [
        'Ext.layout.container.Column'
    ],

	layout: 'column',
    items: [
        {
            xtype: 'textfield',
            reference: 'twitterUsername',
            fieldLabel: 'Twitter Username',
            name: 'username',
            width: 300,
            labelWidth: 150,
            allowBlank: false
        },
        {
            xtype: 'button',
            text: 'Search',
            width: 80,
            margin: '0 10',
            listeners:{
                click: 'searchUsername'
            }
        }
    ]
});