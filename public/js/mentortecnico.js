const createMentoresPanel = () => {

    const mentorStore = Ext.create("Ext.data.Store", {
        storeId: "mentorStore",
        fields: [
            { name: "id", type: "int" },
            { name: "nombre", type: "string" },
            { name: "email", type: "string" },
            { name: "nivelHabilidad", type: "string" },
            { name: "especialidad", type: "string" },
            { name: "experiencia", type: "int" },
            { name: "disponibilidad", type: "string" }
        ],
        autoLoad: true,
        proxy: {
            type: "ajax",
            url: "/api/mentortecnico.php",
            reader: {
                type: "json",
                rootProperty: "data"
            }
        }
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Mentores Técnicos",
        store: mentorStore,
        itemId: "mentorPanel",
        layout: "fit",
        columns: [
            { text: "ID", width: 40, dataIndex: "id" },
            { text: "Nombre", flex: 1, dataIndex: "nombre" },
            { text: "Email", flex: 1, dataIndex: "email" },
            { text: "Nivel Habilidad", flex: 1, dataIndex: "nivelHabilidad" },
            { text: "Especialidad", flex: 1, dataIndex: "especialidad" },
            { text: "Experiencia (años)", flex: 1, dataIndex: "experiencia" },
            { text: "Disponibilidad", flex: 1, dataIndex: "disponibilidad" }
        ]
    });

    return grid;
};

window.createMentoresPanel = createMentoresPanel;
