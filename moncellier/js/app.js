let wines;
const apiURL = 'http://localhost:8888/api'; //'js/wines.json';
const picturesURL = 'http://localhost/caviste/caviste/public/pics/';

window.onload = function() {
    const options = {
        'method':'GET'
    };
    
    fetch(apiURL + '/wines', options).then(function(response) {
        if(response.ok) {
            response.json().then(function(data){
                wines = data;
                
                //afficher la liste de vins
                showListe(wines);
            });
        }
    });

    //Configuration des boutons
    let btSearch = document.getElementById('btSearch');
    btSearch.addEventListener('click', () => search());
    
    let btNewWine = document.getElementById('btNewWine');
    btNewWine.addEventListener('click', () => newWine());
    
    let btSave = document.getElementById('btSave');
    btSave.addEventListener('click', () => saveWine());
};

function saveWine() {
    //creation d'un objet wine
    let wine = {};
    
    //recuperer les données du formulaire et les transferer a l'objet wine 
    let input = document.getElementById('idWine');
    wine.id = input.value;

    input = document.getElementById('name');
    wine.name = input.value;

    input = document.getElementById('grapes');
    wine.garpes = input.value;

    input = document.getElementById('country');
    wine.country = input.value;

    input = document.getElementById('region');
    wine.region = input.value;

    input = document.getElementById('year');
    wine.year = input.value;

    input = document.getElementById('notes');
    wine.notes = input.innerHTML;

    let imgWine = document.getElementById('picture');
    wine.picture = imgWine.src;
    
    //envoyer l'objet wine a l'API en POST ou en PUT
    let method = (wine.id=='') ?'POST':'PUT';
    
    const options = {
        'method': method,
        'body': JSON.stringify(wine),
        'mode': 'cors',
        'headers': {
            'content-type':'application/json; charset=UTF-8'
        }
    };
    
    const fetchURL = method=='PUT' ? '/wines/'+wine.id : '/wines';
    
    fetch(apiURL + fetchURL, options).then(function(response) {
        if(response.ok) {
            response.json().then(function(data){
                if(data){
                    
                }
                
                //afficher la liste de vins
                showListe(wines);
            });
        }
    });
}

function newWine() {
    //Vider le formulaire
    let input = document.getElementById('idWine');
    input.value = '';

    let inputName = document.getElementById('name');
    inputName.value = '';

    input = document.getElementById('grapes');
    input.value = '';

    input = document.getElementById('country');
    input.value = '';

    input = document.getElementById('region');
    input.value = '';

    input = document.getElementById('year');
    input.value = '';

    input = document.getElementById('notes');
    input.innerHTML = '';

    let imgWine = document.getElementById('picture');
    imgWine.src = 'images/pics/generic.jpg';
    
    //Mettre le curseur dans le champ name
    inputName.focus();
}

function showListe(wines) {
    //Sélectionner la liste des vins
    let listeUL = document.getElementById('liste');
    let strLIs = '';

    //Pour chaque vin, créer un LI
    wines.forEach(function(wine) {
        let idWine = wine.id;

        strLIs += '<li data-id="'+idWine+'" class="list-group-item">'+wine.name+'</li>';
    });

    //Insérer tous les LIs dans la liste UL des vins
    listeUL.innerHTML = strLIs;

    //Récupérer tous les LIs
    let nodeLIs = listeUL.getElementsByTagName('li');

    //Ajouter un gestionnaire d'événement sur chaque LI
    for(let li of nodeLIs) {
        li.addEventListener('click',function() { 
            getWine(this.dataset.id, wines);
        });
    }
}

function search() {
    let inputKeyword = document.getElementById('keyword');
    let keyword = inputKeyword.value;   
    
    //Filtrer la liste des vins sur base du keyword
    const regex = new RegExp(keyword, 'i');
    let filteredWines = wines.filter(wine => wine.name && wine.name.search(regex)!=-1);

    //Afficher les vins dans le UL liste
    showListe(filteredWines);
}
    
function getWine(id, wines) {
    let wine = wines.find(wine => wine.id == id);
    
    let input = document.getElementById('idWine');
    input.value = wine.id;

    input = document.getElementById('name');
    input.value = wine.name;

    input = document.getElementById('grapes');
    input.value = wine.grapes;

    input = document.getElementById('country');
    input.value = wine.country;

    input = document.getElementById('region');
    input.value = wine.region;

    input = document.getElementById('year');
    input.value = wine.year;

    input = document.getElementById('notes');
    input.innerHTML = wine.notes;

    let imgWine = document.getElementById('picture');
    imgWine.src = wine.picture!=''
        ? picturesURL+wine.picture
        : 'images/pics/nopics.png';
}