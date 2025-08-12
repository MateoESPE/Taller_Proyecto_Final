const createBooksPanel = () => {

    const authorStore = Ext.getStore("authorStore");
    if (!authorStore) {
        throw new Error("authorStore no encontrado. Cargue el archivo author.js");
    }

    Ext.define("App.model.Book", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "title", type: "string" },
            { name: "description", type: "string" },
            { name: "publicationDate", type: "date", dateFormat: 'Y-m-d' },
            { name: "authorId", mapping: "author.id", type: "int" },
            { 
                name: "authorName",
                convert: (v, rec) => {
                    const a = rec.get("author");
                    return a ? `${a.firstName} ${a.lastName}` : "";
                }
            },
            { name: "isbn", type: "string" },
            { name: "genre", type: "string" },
            { name: "edition", type: "int" }
        ]
    });

    const bookStore = Ext.create("Ext.data.Store", {
        storeId: "bookStore",
        model: "App.model.Book",
        autoLoad: true,
        proxy: {
            type: "ajax",
            url: "/api/book.php",
            reader: {
                type: "json",
                rootProperty: "data"
            }
        }
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Books",
        store: bookStore,
        itemId: "bookPanel",
        layout: "fit",
        columns: [
            {
                text: "ID",
                width: 40,
                sortable: false,
                hideable: false,
                dataIndex: "id"
            },
            {
                text: "Title",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "title"
            },
            {
                text: "Description",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "description"
            },
            {
                text: "Publication Date",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "publicationDate",
                xtype: "datecolumn",
                format: "Y-m-d"
            },
            {
                text: "Author",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "authorName"
            },
            {
                text: "ISBN",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "isbn"
            },
            {
                text: "Genre",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "genre"
            },
            {
                text: "Edition",
                flex: 1,
                sortable: false,
                hideable: false,
                dataIndex: "edition"
            }
        ]
    });

    return grid;
};

window.createBooksPanel = createBooksPanel;
