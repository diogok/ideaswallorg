$(document).ready(start);

var host = "http://www.ideaswall.org";
//var host = "http://localhost:8080";

var Repository={
    callStart : function() {},
    updateCall : function() {},
    insertCall : function() {},
    tags : [],
    ideias : [],
    map : [],
    loaded : [],
    user: "",
    load: function(callback) {
        this.callStart = callback ;
        $.ajaxSetup({cache: false });
        $.get(host+"/users/"+Repository.user+".json",null,Repository.authOk);
    },
    authOk: function(data) {
        $.ajaxSetup({cache: false });
        $.get(host+"/tags.json",null,Repository.loadTags);
        $.get(host+"/ideas.json",null,Repository.loadIdeias);
        $.get(host+"/map.json",null,Repository.loadMap);
    },
    loadTags: function(data) {
        var tags = eval(data);
        for(var i in tags) {
            Repository.tags[i] = tags[i].replace(" ","");
        }
        Repository.loaded("tags");
    },
    loadIdeias: function(data) {
        var ideias = eval(data);
        for(var i in ideias) {
            for(var b in ideias[i].tags) {
                ideias[i].tags[b].tag = ideias[i].tags[b].tag.replace(" ","");
            }
            Repository.ideias[ideias[i].id] = ideias[i];
        }
        Repository.loaded("ideias");
    },
    loadMap: function(data) {
        var maps = eval(data);
        for(var i in maps) {
            Repository.map[maps[i].id_ideas] = maps[i];
        }
        Repository.loaded("map");
    },
    loaded: function(item){
        this.loaded[item] = true ;
        if(this.isLoaded("ideias") && this.isLoaded("map") && this.isLoaded("tags")) {
            this.callStart();
        }
    },
    isLoaded: function(item) {
        // uhm...
        return this.loaded[item];
    },
    addIdeia: function(ideia){
        var c = ideia.id;
        this.ideias[c] = ideia;
    },
    addTag: function(tag){
        var c = this.tags.length;
        this.tags[c] = tag;
    },
    addMap: function(map){
        var c = maps.id_ideas;
        this.maps[c] = map;
    },
    getIdeias: function(){
        return this.ideias ;
    },
    getIdeia: function(id){
        return this.ideias[id];
    },
    getTags: function(){
        return this.tags;
    },
    hasTag: function() {
        for(var i in Repository.tags) {
            if(Repository.tags[i] == ideia.tags[o].tag) {
                return true;
            }
        }
        return false;
    },
    getMaps: function() {
        return this.map;
    },
    getMap: function(id) {
        return this.map[id];
    },
    saveMap: function(x,y,id){
                 var data = {
                     "id": id,
                     "x": x,
                     "y": y  
                 }
        $.post(host+"/ideas/"+id+"/map.json",data);
    },
    updateIdeia: function(id,texto,prioridade,status,tags,call) {
        var url = host+"/ideas/"+id+".json";
        var data = {
            "id": id,
            "idea": texto,
            "priori": prioridade,
            "status": status,
            "tags": tags
        }; 
        this.updateCall = call ;
        $.post(url,data,this.updateIdeiaDone);
    },
    updateIdeiaDone: function(data){
        data = "["+data+"]";
        var ideia = eval(data);
        ideia = ideia[0];
        Repository.updateCall(ideia);
        Repository.addIdeia(ideia);
    },
    insertIdeia: function(texto,prioridade,tags,call) {
        var url = host+"/ideas.json";
        var data = {
            "idea": texto,
            "priori": prioridade,
            "status": 0,
            "tags": tags
        }; 
        this.insertCall = call ;
        $.post(url,data,Repository.insertIdeiaDone);
    },
    insertIdeiaDone: function(data){
        data = "["+data+"]";
        var ideia = eval(data);
        ideia = ideia[0];
        Repository.saveMap(10,10,ideia.id);
        Repository.addIdeia(ideia);
        Repository.insertCall(ideia);
    },
    newIdeia: function(tag) {
        var data = new Date();
        var newIdeia = {
            id: "new",
            idea: "",
            data: data.getTime()/1000 ,
            priori: 0,
            tags: [ { tag: tag.replace(" ","") } ],
            status: 0
        };
        return newIdeia;
    }
}

var Controller= {
    onLoad: function(d) {
        $("#carregando").show();
        Repository.load(this.start);
    },
    start: function() {
        Controller.makeTags(Repository.getTags());
        Controller.makeIdeias(Repository.getIdeias());
        Controller.makeMap(Repository.getIdeias());
        Controller.makeDraggable(Repository.getIdeias());
        MenuBuilder.create();
        TagBuilder.counter();
        $("#carregando").hide();
    },
    makeTags: function(tags) {
         $("#map").append(TagBuilder.createList(tags));
         $("#map").append(TagBuilder.createTabs(tags));
         $("#map").tabs();
    },
    makeIdeias: function(ideias){
        for(var i in ideias){
            this.registerIdeia(ideias[i]);
        }
    },
    registerIdeia: function(ideia){
        for(var i in ideia.tags) {
            var tag = ideia.tags[i].tag.replace(" ","") ;
            var div = DivBuilder.create(ideia);
            $("#tag_"+tag).append(div);
            break;
        }
    },
    makeDraggable: function(ideias) {
        // uhm...      
        $(".ideia").draggable(this.dragOpts());
    },
    makeMap: function(ideias){
        var diff = 10 ;
        for(var i in ideias) {
           var map = Repository.getMap(ideias[i].id) ;
           if(map == null ){
               map = {
                   x: 10 + diff,
                   y: 10 + diff
               }
               diff = diff + 10 ;
           } else {
           }
           map.x = parseInt(map.x);
           map.y = parseInt(map.y);
           if(map.x < 0) {
               map.x = 10 +diff ;
               diff = diff + 10 ;
           }
           if(map.y < 0) {
               map.y = 10 +diff ;
               diff = diff + 10 ;
           }
           var id = "#ideia_"+ map.id_ideas;
           $(id).css("top",map.y );
           $(id).css("left",map.x);
        }
    },
    dragOpts: function() {
        return {
        //"cursor":"pointer",
        "scroll":true,
        //"snap": true,
        "grid": [ 5,5],
        "zIndex": 5,
        //"handle": $(".priori"),
        "containment": $("_map"),
        stop: function(event, ui) {
            Controller.savePos(event,ui);
            }
        };
    },
    savePos: function(event,ui) {
        var x = ui.position.left ;
        var y = ui.position.top ;
        var id = ui.helper.attr("id").substring(6);
        Repository.saveMap(x,y,id);
        return true;
    },
    salvar: function(id,texto,prioridade,status,tags) {
        $("#carregando").show();
        if(id == "new") {
            Repository.insertIdeia(texto,prioridade,tags,Controller.insertEnd);
        } else {
            Repository.updateIdeia(id,texto,prioridade,status,tags,Controller.updateEnd);
        }
        TagBuilder.counter();
    },
    updateEnd: function(ideia) {
        // uhm...
        $("#carregando").hide();
    },
    insertEnd: function(ideia) {
        var div = DivBuilder.create(ideia);
        $(div).css("top","40px");
        $(div).css("position","absolute");
        $("#tag_"+ideia.tags[0].tag).append(div);
        $(div).draggable(Controller.dragOpts());
        $("#carregando").hide();
    },
    showAll: function() {
        TagBuilder.counter();
        $(".ideia").show();
        $(".aTag").show();
    },
    showPendentes: function() {
        TagBuilder.counter();
        $(".aTag").show();
        $(".ideia").show();
        $(".concluida").hide();
        $(".iniciada").hide();
        $(".nopendentes").hide();
    },
    showIniciadas: function() {
        TagBuilder.counter();
        $(".aTag").show();
        $(".ideia").hide();
        $(".iniciada").show();
        $(".noiniciadas").hide();
    },
    showConcluidas: function() {
        TagBuilder.counter();
        $(".aTag").show();
        $(".ideia").hide();
        $(".concluida").show();
        $(".noconcluida").hide();
    },
    createNew: function() {
        var tag = $(".ui-state-active").attr("id").substr(4) ;
        if(tag == undefined) {
            alert("Please select a tag first.");
            return ;
        }
        var newIdeia = Repository.newIdeia(tag) ;
        var div = DivBuilder.create(newIdeia);
        $(div).css("top","40px");
        $(div).css("position","absolute");
        $("#tag_"+tag).append(div);
        $(div).draggable(this.dragOpts());
        DivBuilder.makeEditable(div);
    },
    activeTag: function() {
        var tag = $(".ui-state-active").attr("id").substr(4) ;
        return tag;
    },
    autoMap: function() {
         AutoMap.map(Controller.activeTag());
         return true;
    }
}

var DivBuilder= {
    create: function(ideia) {
        if(ideia.status == 5 || ideia.status == "5") return "";
        var div = document.createElement("div");
        $(div).addClass("ideia").attr("id","ideia_"+ideia.id);
        if(ideia.status == 3 || ideia.status == "3") $(div).addClass("concluida");
        if(ideia.status == 2 || ideia.status == "2") $(div).addClass("iniciada");
        $(div).append(this.status(ideia.status));
        $(div).append(this.prioridade(ideia.priori));
        $(div).append(this.editar(ideia.status));
        $(div).append(this.date(ideia.date));
        $(div).append(this.text(ideia.idea));
        //this.pos(div);
        return div ;
    },
    text: function(text) {
        var span = "<span class='ideiaText'>"+ text + "</span>";
        return span ;
    },
    tags: function(tags) {
        var span = "<span class='ideiaTags'>";
        for(var i in tags) {
          span += tags[i]+" ";
        }
        span += "</span>";
        return span;
    },
    date: function(data) {
        var span = "<span class='ideiaDate'>"+ convertDate( data ) +"</span>";
        return span;
    },
    prioridade: function(p) {
        var priori = "Priority ";
        if(p == 0 || p == "0") priori = "";
        else if(p == 1 || p == "1") priori += "medium";
        else if(p == 2 || p == "2") priori += "high";
        return "<span class='priori P"+p+"'>"+ priori +"</span>";
    },
    editar: function(status) {
        var span = "<span class='ideiaEditar link' onclick='DivBuilder.makeEditable($(this).parent())'>";
        span += "<img src='page_edit.png' alt='edit'/>";
        span += "</span>"
        return span ;
    },
    status: function(status) {
        var span = "<span class='ideiaStatus'>";
        if(status == 3 || status == "3") {
            span += "";
        } else if(status == 2 || status == "2") {
            span += "";
        }
        span += "</span>"
        return span ;
    },
    pos: function(div) {
        div.style.top = '15px';
        return true ;
    },
    selectPriori: function(ideia) {
        var select = "<select>";
        if ( ideia.priori == 2 ) {
            select += "<option selected='selected' value='2'>Priority: High</option>";
        } else {
            select += "<option value='2'>Priority: High</option>";
        }
        if ( ideia.priori == 1 ) {
            select += "<option selected='selected' value='1'>Priority: Medium</option>";
        } else {
            select += "<option value='1'>Priority: Medium</option>";
        }
        if ( ideia.priori == 0 ) {
            select += "<option selected='selected' value='0'>Priority: Common</option>";
        } else {
            select += "<option value='0'>Priority: Common</option>";
        }
        select += "</select>";
        return select;
    },
    selectStatus: function(ideia) {
        var select = "<select>";
        if ( ideia.status == 1 ) {
            select += "<option selected='selected' value='1'>Created</option>";
        } else {
            select += "<option value='1'>Created</option>";
        }
        if ( ideia.status == 2 ) {
            select += "<option selected='selected' value='2'>Ongoing</option>";
        } else {
            select += "<option value='2'>Ongoing</option>";
        }
        if ( ideia.status == 3 ) {
            select += "<option selected='selected' value='3'>Done</option>";
        } else {
            select += "<option value='3'>Done</option>";
        }
        select += "<option value='5'>Trash</option>";
        select += "</select>";
        return select;
    },
    makeEditable: function(div) {
        var id = $(div).attr("id").substring(6);
        var ideia = null ;
        if(id == "new") {
            ideia = Repository.newIdeia(Controller.activeTag());
        } else {
            ideia = Repository.getIdeia(id);
        }
        if ( $(div).hasClass("isEditing") ) {
            this.save(ideia, $(div));
            return ;
        }
        $(div).addClass("isEditing");
        $(".ideiaEditar",div).html("Save");
        var editor = "<textarea cols='45' class='textEditor' rows='2'>"+ideia.idea+"</textarea>";
        var tH = $(".ideiaText",div)[0].offsetHeight - 8 ;
        if(tH < 15) {
            tH = 25;
        }
        $(".ideiaText",div).html(editor);
        $(".ideiaText textarea",div).css("height", tH+ "px");
        $(".priori",div).html(this.selectPriori(ideia));
        $(".ideiaStatus",div).html(this.selectStatus(ideia));
    },
    save: function(ideia,div){
        var status = $(".ideiaStatus select",div).val();
        if(status == 5) {
            var con = confirm("Are you sure about deleting this item?");
            if(!con) {
                return "";
            }
        }
        var texto = $(".ideiaText .textEditor",div).val();
        var prioridade = $(".priori select",div).val();
        var tags = "";
        for(var i in ideia.tags) {
            tags += ", "+ ideia.tags[i].tag;
        }
        tags = tags.substring(1);
        var id = ideia.id;
        Controller.salvar(id,texto,prioridade,status,tags);
        if(status == 3) {
            $(div).addClass("concluida");
            $(div).removeClass("iniciada");
        } else if(status == 2) {
            $(div).removeClass("concluida");
            $(div).addClass("iniciada");
        } else {
            $(div).removeClass("concluida");
            $(div).removeClass("iniciada");
        }
        if(ideia.id == "new" || status == 5) {
            $(div).remove();
        }
        this.makeUneditable(div,status);
    },
    makeUneditable: function(div,status) {
        $(div).removeClass("isEditing");
        $(".ideiaStatus",div).replaceWith(this.status(status));
        var texto = $(".ideiaText textarea",div).val();
        $(".ideiaText",div).html(texto);
        $(".ideiaEditar",div).html("<img src='page_edit.png' alt='edit'/>");
        var prioridade = $(".priori select",div).val();
        $(".priori",div).replaceWith(this.prioridade(prioridade));
    }
}

var TagBuilder= {
    createList: function(tags) {
        this.createTagDiv();
        var ul = document.createElement("ul");
        $(ul).attr("id",'header');
        for(var i in tags) {
            $(ul).append(TagBuilder.singleTagIn(tags[i]));
        }
        return ul;
    },
    singleTagIn: function(tag) {
           var li = document.createElement("li");
           $(li).addClass('aTag');
           $(li).attr("id","tab_"+tag.replace(" ",""));
           var a = document.createElement("a");
           $(li).append(a);
           $(a).attr("href",'#tag_'+tag.replace(" ",""));
           $(a).html(tag.replace(" ",""));
           return li ;
    },
    singleTag: function(tag) {
           var li = document.createElement("li");
           $("#header").append(li);
           $(li).addClass('aTag');
           $(li).attr("id","tab_"+tag.replace(" ",""));
           var a = document.createElement("a");
           $(li).append(a);
           $(a).attr("href",'#tag_'+tag.replace(" ",""));
           $(a).html(tag.replace(" ",""));
           $("#map").append(this.tab(tag));
    },
    link: function(tag) {
          var label = tag ;
          tag = tag.replace(" ","");
          var link = "<li class='aTag' id='tab_"+tag+"'><a href='#tag_"+ tag+"'>"+ label+"</a></li>";
          return link;
    },
    createTabs: function(tags) {
        var tabs = "";
        for(var i in tags) {
            tabs += this.tab(tags[i]);
        }
        return tabs ;
    },
    tab: function(tag){
        tag = tag.replace(" ","");
        var div = "<div id='tag_"+tag+"' class='tab'></div>";
        return div;
    },
    counter: function(){
        $(".tab").each( function(um,ele) {
            var tag = $(ele).attr("id").substring(4);
            TagBuilder.count(tag);
        });
    },
    count: function(tag) {
         var tabId = "tag_"+ tag ;
         var tab = $("#tag_"+tag);
         var total = $(".ideia",tab).length ;
         var concluidas = $(".concluida",tab).length;
         var iniciadas = $(".iniciada",tab).length;
         var pendentes = total - concluidas - iniciadas ;
         $("#tab_"+tag).removeClass("nopendentes");
         $("#tab_"+tag).removeClass("noiniciadas");
         $("#tab_"+tag).removeClass("noconcluidas");
         if(pendentes == 0) {
            $("#tab_"+tag).addClass("nopendentes");
         }
         if(iniciadas == 0) {
            $("#tab_"+tag).addClass("noiniciadas");
         }
         if(concluidas == 0) {
            $("#tab_"+tag).addClass("noconcluidas");
         }
    },
    createTagDiv: function() {
        var form = document.createElement("div");
        form.setAttribute("title","Tag");
        form.setAttribute("id","tagDialog");
        var html = "<div id='tagNew'>Tag: <input type='text' id='tagNewInput' size='15'/>";
        html += "<input type='button' value='Save' onclick='TagBuilder.doCriarTag()'/>";
        html += "<input type='button' value='Cancel' onclick='TagBuilder.cancelCriarTag()'/></div>";
        $(form).html(html);
        $(form).dialog({
                "width": 380
                });
        $(form).dialog("close");
        $(form).parent().css("height","60px");
    },
    openCriarTag: function() {
        $("#tagDialog").dialog("open");
        return true ;
    },
    doCriarTag: function(){
        var tag = $("#tagNewInput").val();
        TagBuilder.singleTag(tag);
        $("#map").tabs("destroy");
        $("#map").tabs();
        $("#tagDialog").dialog("close");
    },
    cancelCriarTag: function(){
        $("#tagDialog").dialog("close");
        return true ;
    }
}

var MenuBuilder={
    create: function(){
        var ul = "<li class='mI'><a onclick='MenuBuilder.showMenu(); return false'>Menu</a><ul id='menu'></ul></li>";
        $("#header").prepend(ul);
        $("#menu").append(this.makeLink("Auto organize","Controller.autoMap"));
        $("#menu").append(this.makeLink("Create Idea","Controller.createNew"));
        $("#menu").append(this.makeLink("Create Tag","TagBuilder.openCriarTag"));
        $("#menu").append(this.makeLink("See all","Controller.showAll"));
        $("#menu").append(this.makeLink("See only TODO","Controller.showPendentes"));
        $("#menu").append(this.makeLink("See only Ongoing","Controller.showIniciadas"));
        $("#menu").append(this.makeLink("See only Done","Controller.showConcluidas"));
        //$("#menu").append(this.makeLink("Ver Prioridade Alta","Controller.showAlta"));
        $("#menu").hide();
    },
    makeLink: function(text,callback) {
        var link = "<li class='link' onclick='"+callback+"() ; MenuBuilder.showMenu()'>"+text+"</li>";
        return link ;
    },
    showMenu: function() {
        $("#menu").toggle();
    }
}

var AutoMap = {
    last0: 0,
    map: function(tag) {

       var tab = $("#tag_"+tag);
       var list = this.clean(Repository.getIdeias(),tag) ;

       var hTop = document.getElementById("header").offsetHeight ; //height of the header

       this.last0 = hTop + 10 ; 

       var tabWidth = document.getElementById("tag_"+tag).offsetWidth ; // Max width

       list.sort(this.sorter);
       for(var i in list) {
           if(list[i].id.length >= 1 && list[i].status != 5) {
               var ideia = list[i];
               var offset = document.getElementById("ideia_"+list[i].id).offsetHeight;
               var pos = this.fixOrder(ideia,offset);
               var left = this.fixColumn(ideia,offset);
               Repository.saveMap(left,pos,ideia.id);
           }
       }
    },
    sorter: function(i1,i2) {
        if(i1.status < i2.status ) return -1 ;
        if(i1.status > i2.status ) return 1 ;
        if(i1.priori > i2.priori) return -1 ;
        if(i1.priori < i2.priori) return 1 ;
        if(i1.data < i2.data) return -1 ;
        if(i1.data > i2.data) return 1 ;
        return 0 ;
    },
    clean: function(ideias,tag) {
        var list = [];
        for(var i  in ideias) {
           if(ideias[i].tags[0] == undefined) {
               continue;
           }
           if(ideias[i].tags[0].tag == tag) {
               list[list.length] = ideias[i];
           }
        }
        return list ;
    },
    fixColumn: function(ideia,offset) {
        var div = $("#ideia_"+ideia.id);
        var left = 10 ;
        $(div).css("left",left+"px");
        return left;
    },
    fixOrder: function(ideia,offset) {
        var div = $("#ideia_"+ideia.id);
       var pos = this.last0 + 20 ;
       this.last0 = pos + offset ;
        $(div).css("top",pos+"px");
        return pos ;
    },
    n1: function(id) {
    },
    n2: function(id) {
    },
    n3: function(id) {
    }
}

function start() {
    Repository.user = "any";
    Controller.onLoad();
}
