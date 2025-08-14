Ext.onReady(() => {

    // Crear los paneles
    const estudiantesPanel = createEstudiantesPanel();
    const mentoresPanel = createMentoresPanel();
    const equiposPanel = createEquiposPanel();
    const retosRealesPanel = createRetoRealPanel();
    const retosSolPanel = createRetoSolucionablePanel();

    // Panel principal con layout card
    const mainCard = Ext.create('Ext.panel.Panel', {
        region: 'center',
        layout: 'card',
        items: [
            estudiantesPanel,
            mentoresPanel,
            equiposPanel,
            retosRealesPanel,
            retosSolPanel
        ],
    });

    // Crear viewport con toolbar
    Ext.create("Ext.container.Viewport", {
        id: "mainViewport",
        layout: "border",
        items: [
            {
                region: 'north',
                xtype: 'toolbar',
                items: [
                    {
                        text: 'Estudiantes',
                        handler: () => mainCard.getLayout().setActiveItem(estudiantesPanel),
                    },
                    {
                        text: 'Mentores Técnicos',
                        handler: () => mainCard.getLayout().setActiveItem(mentoresPanel),
                    },
                    {
                        text: 'Equipos',
                        handler: () => mainCard.getLayout().setActiveItem(equiposPanel),
                    },
                    {
                        text: 'Retos Reales',
                        handler: () => mainCard.getLayout().setActiveItem(retosRealesPanel),
                    },
                    {
                        text: 'Retos Solucionables',
                        handler: () => mainCard.getLayout().setActiveItem(retosSolPanel),
                    }
                ]
            },
            mainCard
        ],
    });

});
