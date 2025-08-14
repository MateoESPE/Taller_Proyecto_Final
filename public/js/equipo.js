const createEquiposPanel = () => {

    const equipoStore = Ext.create("Ext.data.Store", {
        storeId: "equipoStore",
        fields: [
            { name: "id", type: "int" },
            { name: "nombre", type: "string" },
            { name: "hackathonId", type: "string" }
        ],
        autoLoad: true,
        proxy: {
            type: "ajax",
            url: "/api/equipo.php",
            reader: {
                type: "json",
                rootProperty: "data"
            }
        }
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Equipos",
        store: equipoStore,
        itemId: "equipoPanel",
        layout: "fit",
        columns: [
            { text: "ID", width: 40, dataIndex: "id" },
            { text: "Nombre", flex: 1, dataIndex: "nombre" },
            { text: "Hackathon ID", flex: 1, dataIndex: "hackathonId" }
        ]
    });

    return grid;
};

window.createEquiposPanel = createEquiposPanel;
