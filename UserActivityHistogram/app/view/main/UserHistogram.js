Ext.define('UserActivityHistogram.view.main.UserHistogram', {
    extend: 'Ext.chart.CartesianChart',
    alias: 'widget.user-histogram',
    requires: [
    	'Ext.chart.CartesianChart',
        'Ext.chart.axis.Category',
        'Ext.chart.series.Bar',
        'Ext.chart.axis.Numeric',
        'Ext.chart.interactions.ItemHighlight'
    ],

    reference: 'chart',
    store: 'Tweet',
    height: 300,
    interactions: 'itemhighlight',
    insetPadding: {
        top: 40,
        bottom: 40,
        left: 0,
        right: 40
    },
    axes: [
        {
            type: 'numeric',
            position: 'left',
            minimum: 0,
            titleMargin: 20,
            title: {
                text: 'The number of tweets'
            },
            fields: 'count'
        }, 
        {
            type: 'category',
            position: 'bottom',
            fields: 'hour'
        }
    ],
    animation: Ext.isIE8 ? false : true,
    series: {
        type: 'bar',
        xField: 'hour',
        yField: 'count',
        style: {
            minGapWidth: 20
        }
        ,
        highlight: {
            strokeStyle: 'black',
            fillStyle: 'gold',
            lineDash: [5, 3]
        },
        label: {
            field: 'count',
            display: 'insideEnd',
            renderer: function (value) {
                return value;
            }
        }
    },
    sprites: {
        type: 'text',
        text: "Last 500 tweets by hours",
        fontSize: 22,
        width: 100,
        height: 30,
        x: 40, // the sprite x position
        y: 20  // the sprite y position
    },

    listeners:{
    	boxready: 'boxreadyHandler',
    	scope: 'this'
    },

    boxreadyHandler: function(){
    	this.updateTitle(null);
    },

    updateTitle: function(username){
    	if(Ext.isEmpty(username)){
			username = 'User';
    	}
    	// this.setTitle(username + "'s last 500 tweets Data");
    } 
});