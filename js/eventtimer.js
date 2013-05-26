var events_url = 'gettimerv4.php';
var worlds_url = 'https://api.guildwars2.com/v1/world_names.json';
var event_names_url = 'https://api.guildwars2.com/v1/event_names.json';

var dragon_events = {
    '580A44EE-BAED-429A-B8BE-907A18E36189': 'Shatterer',
    '568A30CF-8512-462F-9D67-647D69BEFAED': 'Tequatl the Sunless',
    '0464CB9E-1848-4AAA-BA31-4779A959DD71': 'Jormag'
};

var main_events = {
    'F7D9D427-5E54-4F12-977A-9809B23FBA99': 'The Frozen Maw',
    '31CEBA08-E44D-472F-81B0-7143D73797F5': 'Shadow Behemoth',
    '33F76E9E-0BB6-46D0-A3A9-BE4CDFC4A3A4': 'Fire Elemental',
    'C5972F64-B894-45B4-BC31-2DEEA6B7C033': 'Great Jungle Wurm',
    'C876757A-EF3E-4FBE-A484-07FF790D9B05': 'Megadestroyer',
    '9AA133DC-F630-4A0E-BB5D-EE34A2B306C2': 'Golem Mark II',
    'D35D7F3B-0A9B-41C6-BD87-7D7A0953F789': 'Temple of Balthazar',
    '57A8E394-092D-4877-90A5-C238E882C320': 'Temple of Grenth',
    '0372874E-59B7-4A8F-B535-2CF57B8E67E4': 'Temple of Lyssa'
};

var all_events = {};

var states = {
    'Active': 'activated',
    'Success': 'success',
    'Fail': 'fail',
    'Warmup': 'waiting',
    'Preparation': 'waiting',
    'Spawn Window': 'waiting',
    'Unknown': 'inactivated'
};

var labels = {
    '580A44EE-BAED-429A-B8BE-907A18E36189': {
        'Active': 'Fim do evento: ',
        'Warmup': 'Início do pré-evento: ',
        'Unknown': 'Desculpe, tempo desconhecido...'
    },
    '568A30CF-8512-462F-9D67-647D69BEFAED': {
        'Active': 'Fim do evento: ',
        'Warmup': 'Início da spawning window: ',
        'Spawn Window': 'Fim da spawning window: ',
        'Unknown': 'Desculpe, tempo desconhecido...'
    },
    '0464CB9E-1848-4AAA-BA31-4779A959DD71': {
        'Success': 'Início da spawning window: ',
        'Active': 'Fim do evento: ',
        'Spawn Window': 'Fim da spawning window: ',
        'Unknown': 'Desculpe, tempo desconhecido...'
    }
};

var globalTimer;

var worldId;

function deleteCookie(c_name) {
    document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
};

function setCookie(c_name,value,exdays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
    document.cookie=c_name + "=" + c_value;
}

function getCookie(c_name)
{
    var c_value = document.cookie;
    var c_start = c_value.indexOf(" " + c_name + "=");
    if (c_start == -1)
    {
        c_start = c_value.indexOf(c_name + "=");
    }
    if (c_start == -1)
    {
        c_value = null;
    }
    else
    {
        c_start = c_value.indexOf("=", c_start) + 1;
        var c_end = c_value.indexOf(";", c_start);
        if (c_end == -1)
        {
            c_end = c_value.length;
        }
        c_value = unescape(c_value.substring(c_start,c_end));
    }
    return c_value;
}

getEventsQuery = function(world, events) {
    query = 'world='+worldId+'&event=';
    for(event in events) query += event + ',';
    return query;
}
updateData = function(data) {
    
    window.clearTimeout(globalTimer);
    globalTimer=window.setTimeout(function(){
        $.ajax({
            type: 'GET',
            data: getEventsQuery(worldId, dragon_events),
            url: events_url,
            async: true,
            dataType: 'json', 
            success: function (json) {
                updateData(json);
            }
        });
    },60000);
  
    data = data.events;
    for (key in data) {
        evnt = data[key];
        var id = null;
        //Atualizar dragons
        if (evnt.event_id in dragon_events) id="#drake_"+dragon_events[evnt.event_id].replace(/ /g,'_');
    
        //Atualizar main_events
        //if (eventId in main_events) id="#main_"+main_events[eventId].replace(/ /g,'_');
    
        //Atualizar all_events
        //if (eventId in all_events) id="#all_"+all_events[eventId].replace(/ /g,'_');

        $(id+' .inner_event .timer').remove();
        $(id+' .inner_event').append('<div class="timer"></div>');
        //Spawn timer se o evento estiver na página
        state = evnt.state;
        if(id){
            timer = evnt.remaining_spawn;
            if(timer > 0){
                $(id+' .inner_event .timer').chrony({
                    seconds: parseInt(timer)
                    });
            }else {
                timer = evnt.remaining_window;
                state = 'Spawn Window';
                if(timer > 0){
                    $(id+' .inner_event .timer').chrony({
                        seconds: parseInt(timer)
                        });
                } else {
                    timer = 0;
                    state = 'Unknown';
                }
            }
            $(id+' .inner_event .label').html(labels[evnt.event_id][state]);
        }
        $(id).removeClass();
        $(id).addClass(states[evnt.state]);
    }
}

//Carregar eventos
$.ajax({
    type: 'GET',
    url: event_names_url,
    dataType: 'json',
    success: function(data){
        $.each(data, function(key,obj) {
            all_events[obj.id] = obj.name;
        });
    }
});

//Carregar servidores
$.ajax({
    type: 'GET',
    url: worlds_url,
    dataType: 'json',
    success: function(data){
        items = [];
        data = data.sort(function(a,b){
            var nameA=a.name.toLowerCase(), nameB=b.name.toLowerCase();
            if (nameA < nameB) return -1;
            if (nameA > nameB) return 1;
            return 0;
        });
        worldId = getCookie('worldId');
        $.each(data, function(key, obj) {
            if(worldId==obj.id) {
                items.push('<option value="'+ obj.id +'" selected>' + obj.name + '</option>');
            } else {
                if(!worldId && obj.id == 1013){
                    items.push('<option value="1013" selected>' + obj.name + '</option>');
                } else {
                    items.push('<option value="'+ obj.id +'">' + obj.name + '</option>');
                }
            }
        });
        changeWorld = function() {
            worldId = $('#world-list').val();
            $('.modal').fadeIn();
            $.ajax({
                type: 'GET',
                data: getEventsQuery(worldId, dragon_events),
                url: events_url,
                async: true,
                dataType: 'json', 
                success: function (json) {
                    deleteCookie('worldId');
                    setCookie('worldId', worldId, 4);
                    $('.modal').fadeOut();
                    updateData(json);
                }
            });
        }
        $('#world-list').append(items);
        changeWorld();
        $('#world-list').change(changeWorld);
    }
});
