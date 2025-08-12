const createArticlesPanel = () => {

    const authorStore = Ext.getStore("authorStore");
    if (!authorStore) {
        throw new Error("authorStore no encontrado. Cargue el archivo author.js");
    }

    Ext.define("App.model.Article", {
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
            { name: "doi", type: "string" },
            { name: "abstract", type: "string" },
            { name: "keywords", type: "string" },
            { name: "indexation", type: "string" },
            { name: "magazine", type: "string" },
            { name: "area", type: "string" }
        ]
    });

    const articleStore = Ext.create("Ext.data.Store", {
        storeId: "articleStore",
        model: "App.model.Article",
        autoLoad: true,
        proxy: {
            type: "ajax",
            url: "/api/article.php",
            reader: {
                type: "json",
                rootProperty: "data"
            }
        }
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Articles",
        store: articleStore,
        itemId: "articlePanel",
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
                text: "DOI", 
                flex: 1, 
                sortable: false,
                hideable: false,
                dataIndex: "doi" 
            },
            { 
                text: "Abstract", 
                flex: 1, 
                sortable: false,
                hideable: false,
                dataIndex: "abstract" 
            },
            { 
                text: "Keywords", 
                flex: 1, 
                sortable: false,
                hideable: false,
                dataIndex: "keywords" 
            },
            { 
                text: "Indexation", 
                flex: 1, 
                sortable: false,
                hideable: false,
                dataIndex: "indexation" 
            },
            { 
                text: "Magazine", 
                flex: 1, 
                sortable: false,
                hideable: false,
                dataIndex: "magazine" 
            },
            { 
                text: "Area", 
                flex: 1, 
                sortable: false,
                hideable: false,
                dataIndex: "area" 
            }
        ]
    });

    return grid;
};

window.createArticlesPanel = createArticlesPanel;
