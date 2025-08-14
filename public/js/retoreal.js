const createRetoRealPanel = () => {

    const retoRealStore = Ext.create("Ext.data.Store", {
        storeId: "retoRealStore",
        fields: [
            { name: "id", type: "int" },
            { name: "nombre", type: "string" },
            { name: "descripcion", type: "string" },
            { name: "dificultad", type: "string" }
        ],
        autoLoad: true,
        proxy: {
            type: "ajax",
            url: "/api/retoreal.php",
            reader: {
                type: "json",
                rootProperty: "data"
            }
        }
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Retos Reales",
        store: retoRealStore,
        itemId: "retoRealPanel",
        layout: "fit",
        columns: [
            { text: "ID", width: 40, dataIndex: "id" },
            { text: "Nombre", flex: 1, dataIndex: "nombre" },
            { text: "Descripción", flex: 1, dataIndex: "descripcion" },
            { text: "Dificultad", flex: 1, dataIndex: "dificultad" }
        ]
    });

    return grid;
};

window.createRetoRealPanel = createRetoRealPanel;
