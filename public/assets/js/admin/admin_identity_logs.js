ajax.get('/api/identities/'+window.id+"/logs",function(data) {
    gdg = new GrapheneDataGrid({el:'#adminDataGrid',
    item_template: gform.stencils['table_row'],
    search: false,columns: false,upload:false,download:false,title:'Systems',
    sort:'created_at',
    actions:[
    ],
    count:20,
    schema:[
        {type:"hidden", name:"id"},
        {type:"select", name:"action", label:"Action",
            options:[
                    {label:"Add",value:"add"},
                    {label:"Delete",value:"delete"},
                    {label:"Update",value:"update"},
                    {label:"Restore",value:"restore"},
                    {label:"Disable",value:"disable"}
            ]
        },
        {type:"text", name:"actor_identity_id", label:"Actor",template:"{{attributes.actor.first_name}} {{attributes.actor.last_name}}"},
        {type:"select", name:"type", label:"Type",
            options:[
                {label:"Membership",value:"membership"},
                {label:"Account",value:"account"},
                {label:"Entitlement",value:"entitlement"}
        ]},
        {type:"text", name:"type_name", label:"Name"},
        {type:"text", name:"data", label:"Account"},
        {type:"date",name:"created_at",label:"Created at"}
    ], data: data
    });
});