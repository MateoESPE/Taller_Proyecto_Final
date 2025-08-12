Ext.onReady(()=>{

    const authorsPanel = createAuthorsPanel();
    const booksPanel =  createBooksPanel();
    const articlesPanel = createArticlesPanel();

    const mainCard = Ext.create('Ext.panel.Panel', {
        region: 'center',
        layout: 'card',
        items: [authorsPanel, booksPanel,articlesPanel],
    });

    Ext.create("Ext.container.Viewport", {
        id: "mainViewport",
        layout: "border",
        items: [
            {
                region: 'north',
                xtype: 'toolbar',
                items: [
                    {
                        text: 'Authors',
                        handler: ()=>mainCard.getLayout().setActiveItem(authorsPanel),
                    },
                    {
                        text: 'Books',
                        handler: ()=>mainCard.getLayout().setActiveItem(booksPanel),
                    },
                    {
                        text: 'Articles',
                        handler: ()=>mainCard.getLayout().setActiveItem(articlesPanel),
                    }
                ]
            },
            mainCard
        ],
    });

})