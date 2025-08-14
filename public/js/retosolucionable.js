const createRetoSolucionablePanel = () => {

    const retoSolStore = Ext.create("Ext.data.Store", {
        storeId: "retoSolStore",
        fields: [
            { name: "id", type: "int" },
            { name: "nombre", type: "string" },
            { name: "descripcion", type: "string" },
            { name: "tipo", type: "string" } // por ejemplo: "experimental" o "teórico"
        ],
        autoLoad: true,
        proxy: {
            type: "ajax",
            url: "/api/retosolucionable.php",
            reader: {
                type: "json",
                rootProperty: "data"
            }
        }
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Retos Solucionables",
        store: retoSolStore,
        itemId: "retoSolPanel",
        layout: "fit",
        columns: [
            { text: "ID", width: 40, dataIndex: "id" },
            { text: "Nombre", flex: 1, dataIndex: "nombre" },
            { text: "Descripción", flex: 1, dataIndex: "descripcion" },
            { text: "Tipo", flex: 1, dataIndex: "tipo" }
        ]
    });

    return grid;
};

window.createRetoSolucionablePanel = createRetoSolucionablePanel;
