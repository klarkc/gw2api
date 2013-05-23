var events_url = 'gettimer.php';
var worlds_url = 'https://api.guildwars2.com/v1/world_names.json';
var event_names_url = 'https://api.guildwars2.com/v1/event_names.json';

var dragon_events = {
  '03BF176A-D59F-49CA-A311-39FC6F533F2F': 'Shatterer',
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
  'Inactive': 'inactivated'
};

var globalTimer;

updateData = function(worldId) {

  var data = {}; 
  
  $.ajax({
    type: 'GET',
    data: 'world_id=' + worldId,
    url: events_url,
    async: false,
    dataType: 'json', 
    success: function (json) {
      data = json;
    }
  });

  for(var i in data) {
    var obj = data[i];
    eventId = obj.event.event_id;

    lastTime = new Date(Date.parse(obj.lastspawn));
    nowTime = new Date();
    nowTime = new Date(nowTime.toUTCString());

    //Sistema de Spawn provisorio
    spawnTime = lastTime;
    label = '';
    switch(obj.event.state) {
	case 'Success':; time=3*60*60*1000; label = "Spawn em: "; break;
	case 'Fail': time=3*60*60*1000; label = "Spawn em: "; break;
	case 'Active': time=10*60*1000; label = "Finaliza em: ";  break;
	case 'Warmup': time=10*60*1000; label = "Inicia em: "; break;
	case 'Preparation': time=3*60*1000; label = "Inicia em: "; break;
    }
    spawnTime.setTime(lastTime.getTime()+time);   
    difference = spawnTime - nowTime;
    reamingTime = {}
    if(Math.floor(difference / 36e5 ) > 0 ){
	reamingTime["hour"] = Math.floor(difference / 36e5);
    }
    if(Math.floor(difference % 36e5 / 60000) > 0){
	reamingTime["minute"] = Math.floor(difference % 36e5 / 60000);
    }
    if( Math.floor(difference % 60000 / 1000) > 0 ){
        reamingTime["second"] =  Math.floor(difference % 60000 / 1000);
    }

    var id = 'nothing';

    //Atualizar dragons
    if (eventId in dragon_events) id="#drake_"+dragon_events[eventId].replace(/ /g,'_');;
    
    //Atualizar main_events
    //if (eventId in main_events) id="#main_"+main_events[eventId].replace(/ /g,'_');
    
    //Atualizar all_events
    //if (eventId in all_events) id="#all_"+all_events[eventId].replace(/ /g,'_');

    $(id+' .inner_event .timer').remove();
    //Spawn timer se o evento estiver na página
    if(id != 'nothing'){
	    $(id+' .inner_event').append('<div class="timer"><span class="label">'+label+'</span></div>');
	    $(id+' .inner_event .timer').chrony(reamingTime);
    }
    $(id).removeClass();
    $(id).addClass(states[obj.event.state]);
  }

  window.clearTimeout(globalTimer);
  globalTimer=window.setTimeout(function(){updateData(worldId);},60000);

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
    $.each(data, function(key, obj) {
      items.push('<option value="'+ obj.id +'">' + obj.name + '</option>');
    });
    
    $('#world-list').append(items);
    $('#world-list').change(function() {
      updateData($(this).val());
    });
  }
});
