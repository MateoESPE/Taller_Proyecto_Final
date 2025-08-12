const createAuthorsPanel = () => {
    Ext.define("App.model.Author", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id",         type: "int" },
            { name: "firstName",  type: "string" },
            { name: "lastName",   type: "string" },
            { name: "username",   type: "string" },
            { name: "email",      type: "string" },
            { name: "password",   type: "string" },
            { name: "orcid",      type: "string" },
            { name: "affiliation", type: "string" },
        ]
    });
    let authorStore = Ext.create("Ext.data.Store", {
        storeId: "authorStore",
        model: "App.model.Author",
        proxy: {
            type             : "rest",
            url              : "/api/author.php",
            reader           : {type: "json", rootProperty:''},
            writer           : {type:'json', rootProperty:'', writeAllFields: true},
            appendId         : false
        },
        autoLoad: true,
        autoSync: false,
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title      : "Authors",
        store      : authorStore,
        itemId     : "authorPanel",
        layout     : "fit",
        columns    : [
            {
                text      : "ID",
                width      : 40,
                sortable  : false,
                hideable  : false,
                dataIndex : "id",
            },
            {
                text      : "First Name",
                flex      : 1,
                sortable  : false,
                hideable  : false,
                dataIndex : "first_name",
            },
            {
                text      : "Last Name",
                flex      : 1,
                sortable  : false,
                hideable  : false,
                dataIndex : "last_name",
            },
            {
                text      : "Username",
                flex      : 1,
                sortable  : false,
                hideable  : false,
                dataIndex : "username",
            },
            {
                text      : "Email",
                flex      : 1,
                sortable  : false,
                hideable  : false,
                dataIndex : "email",
            },
            {
                text      : "Password",
                flex      : 1,
                sortable  : false,
                hideable  : false,
                dataIndex : "password",
            },
            {
                text      : "ORCID",
                flex      : 1,
                sortable  : false,
                hideable  : false,
                dataIndex : "orcid",
            },
            {
                text      : "Affiliation",
                flex      : 1,
                sortable  : false,
                hideable  : false,
                dataIndex : "affiliation",
            },
        ],
    });
    return grid;
};