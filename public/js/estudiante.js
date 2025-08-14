const createEstudiantesPanel = () => {

    const estudianteStore = Ext.create("Ext.data.Store", {
        storeId: "estudianteStore",
        fields: [
            { name: "id", type: "int" },
            { name: "nombre", type: "string" },
            { name: "email", type: "string" },
            { name: "nivelHabilidad", type: "string" },
            { name: "grado", type: "string" },
            { name: "institucion", type: "string" },
            { name: "tiempoDisponibleSemanal", type: "int" }
        ],
        autoLoad: true,
        proxy: {
            type: "ajax",
            url: "/api/estudiante.php",
            reader: {
                type: "json",
                rootProperty: "data"
            }
        }
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Estudiantes",
        store: estudianteStore,
        itemId: "estudiantePanel",
        layout: "fit",
        columns: [
            { text: "ID", width: 40, dataIndex: "id" },
            { text: "Nombre", flex: 1, dataIndex: "nombre" },
            { text: "Email", flex: 1, dataIndex: "email" },
            { text: "Nivel Habilidad", flex: 1, dataIndex: "nivelHabilidad" },
            { text: "Grado", flex: 1, dataIndex: "grado" },
            { text: "Institucion", flex: 1, dataIndex: "institucion" },
            { text: "Tiempo Disponible", flex: 1, dataIndex: "tiempoDisponibleSemanal" }
        ]
    });

    return grid;
};

window.createEstudiantesPanel = createEstudiantesPanel;
