ajax.get('/api/entitlements',function(data) {
    gdg = new GrapheneDataGrid({el:'#adminDataGrid',
    item_template: gform.stencils['table_row'],
    search: false,columns: false,upload:false,download:false,title:'Entitlements',
    entries:[],
    actions:[
        {"name":"create","label":"New Entitlement"},
        '',
        {"name":"edit","label":"Update Entitlement"},
        {"label":"Manage Groups","name":"manage_groups","min":1,"max":1,"type":"default"},
        '',
        {"name":"delete","label":"Delete Entitlement"}
    ],
    count:20,
    schema:[
        {type:"hidden", name:"id"},
        {type:"text", name:"name", label:"Name",required:true},
    ], data: data
    }).on("model:edited",function(grid_event) {
        ajax.put('/api/entitlements/'+grid_event.model.attributes.id,grid_event.model.attributes,function(data) {
            grid_event.model.update(data)
        },function(data) {
            grid_event.model.undo();
        });
    }).on("model:created",function(grid_event) {
        ajax.post('/api/entitlements',grid_event.model.attributes,function(data) {
            grid_event.model.update(data)
            // grid_event.model.attributes = data;
        },function(data) {
            grid_event.model.undo();
        });
    }).on("model:manage_groups",function(grid_event) {
        window.location = '/admin/entitlements/'+grid_event.model.attributes.id+'/groups';
    }).on("model:deleted",function(grid_event) {
        ajax.delete('/api/entitlements/'+grid_event.model.attributes.id,{},function(data) {},function(data) {
            grid_event.model.undo();
        });
    });
});
