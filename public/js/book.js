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
            type: 'ajax',
            api: {
                read: '/api/book.php',
                create: '/api/book.php',
                update: '/api/book.php',
                destroy: '/api/book.php'   // <-- Esto es clave
            },
            reader: {
                type: 'json',
                rootProperty: 'data'
            },
            writer: {
                type: 'json',
                writeAllFields: true,
                rootProperty: 'data'
            }
        }
    });

    const authorComboboxCfg = {
        xtype: 'combobox',
        name: 'authorId',
        fieldLabel: 'Author',
        store: authorStore,
        queryMode: 'local',
        valueField: 'id',
        displayField: 'fullName',
        forceSelection: true,
        allowBlank: false
    };

    const openDialog = (rec, isNew) => {
        const win = Ext.create('Ext.window.Window', {
            title: isNew ? 'Add Book' : 'Edit Book',
            modal: true,
            width: 640,
            layout: 'fit',
            items: [{
                xtype: 'form',
                bodyPadding: 12,
                defaults: { anchor: '100%' },
                items: [
                    { xtype: 'hiddenfield', name: 'id' },
                    { xtype: 'textfield', name: 'title', fieldLabel: 'Title', allowBlank: false },
                    { xtype: 'textareafield', name: 'description', fieldLabel: 'Description' },
                    {
                        xtype: 'datefield',
                        name: 'publicationDate',
                        fieldLabel: 'Publication Date',
                        format: 'Y-m-d',
                        submitFormat: 'Y-m-d'
                    },
                    authorComboboxCfg,
                    { xtype: 'textfield', name: 'isbn', fieldLabel: 'ISBN' },
                    { xtype: 'textfield', name: 'genre', fieldLabel: 'Genre' },
                    { xtype: 'numberfield', name: 'edition', fieldLabel: 'Edition' }
                ],
                buttons: [
                    {
                        text: "Save",
                        handler() {
                            const form = this.up("form").getForm();
                            if (!form.isValid()) return;

                            form.updateRecord(rec);
                            if (isNew) bookStore.add(rec);

                            bookStore.sync({
                                success: () => {
                                    Ext.Msg.alert('Success', 'Book Saved');
                                    win.close();
                                },
                                failure: () => {
                                    Ext.Msg.alert("Error", "Save Failed");
                                }
                            });
                        }
                    },
                    {
                        text: "Cancel",
                        handler() {
                            win.close();
                        }
                    }
                ]
            }]
        });

        win.down('form').loadRecord(rec);
        win.show();
    };

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Books",
        store: bookStore,
        itemId: "bookPanel",
        layout: "fit",
        columns: [
            { text: "ID", width: 40, dataIndex: "id" },
            { text: "Title", flex: 1, dataIndex: "title" },
            { text: "Description", flex: 1, dataIndex: "description" },
            { text: "Publication Date", flex: 1, dataIndex: "publicationDate", xtype: "datecolumn", format: "Y-m-d" },
            { text: "Author", flex: 1, dataIndex: "authorName" },
            { text: "ISBN", flex: 1, dataIndex: "isbn" },
            { text: "Genre", flex: 1, dataIndex: "genre" },
            { text: "Edition", flex: 1, dataIndex: "edition" }
        ],
        tbar: [
            {
                text: 'Add',
                handler: () => openDialog(Ext.create('App.model.Book'), true)
            },
            {
                text: 'Edit',
                handler() {
                    const selection = grid.getSelectionModel().getSelection();
                    if (!selection || selection.length === 0) {
                        Ext.Msg.alert('Warning', 'Select a book to edit');
                        return;
                    }
                    openDialog(selection[0], false);
                }
            },
            {
                text: 'Delete',
                handler() {
                    const selection = grid.getSelectionModel().getSelection();
                    if (!selection || selection.length === 0) {
                        Ext.Msg.alert('Warning', 'Select a book to delete');
                        return;
                    }

                    const rec = selection[0];

                    Ext.Msg.confirm('Confirm', 'Delete this book?', btn => {
                        if (btn === 'yes') {
                            bookStore.remove(rec);
                            bookStore.sync({
                                success: () => Ext.Msg.alert('Success', 'Deleted'),
                                failure: () => Ext.Msg.alert('Error', 'Delete failed')
                            });
                        }
                    });
                }
            }
        ]
    });

    return grid;
};

window.createBooksPanel = createBooksPanel;
