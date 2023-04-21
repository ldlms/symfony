//recup la div
let container = document.querySelector('#container');
let url = 'https://localhost:8000/api/article/all';
let url2 = 'https://localhost:8000/api/article/delete';


async function getArticles(){
    const json = await fetch(url);
    const articles = await json.json();
    articles.forEach(obj =>{
        console.log(obj.id)
        let article = document.createElement('div');
        article.setAttribute('id',obj.id);
        container.appendChild(article);
        const titre = document.createElement('h1');
        titre.setAttribute('class',obj.titre);
        titre.textContent = obj.titre;
        article.appendChild(titre);
        const contenu = document.createElement('p');
        contenu.setAttribute('id',obj.contenu);
        article.appendChild(contenu);
        contenu.innerText = obj.contenu;
        const date = document.createElement('p');
        date.setAttribute('id',obj.date);
        date.innerText = obj.date;
        article.appendChild(date);
    })
}

setTimeout(()=>{
    fetch(url)
    .then(async response =>{
        const data = await response.json();
        if(response.status==200){
        data.forEach(obj =>{
            console.log(obj.id)
            let article = document.createElement('div');
            article.setAttribute('class','article');
            container.appendChild(article);
            const titre = document.createElement('h1');
            titre.setAttribute('class',obj.titre);
            titre.textContent = obj.titre;
            article.appendChild(titre);
            const contenu = document.createElement('p');
            contenu.setAttribute('id',obj.contenu);
            article.appendChild(contenu);
            contenu.innerText = obj.contenu;
            const date = document.createElement('p');
            date.setAttribute('id',obj.date);
            date.innerText = obj.date;
            article.appendChild(date);
            const trash = document.createElement('i');
            trash.setAttribute('class','fa-solid fa-trash-can')
            trash.setAttribute('id',obj.id);
            article.appendChild(trash);
            charge = true;
            trash.addEventListener('click',()=>{
                console.log(trash.id);
                fetch(url2+trash.id,{method:'DELETE'})
                .then(async response1 =>{
                    if(response1.status == 200){
                        const datasup = await response1.json();
                        article.remove();
                    }
                })
            })
            

        });
        }else if(response.status==206){
            container.textContent= data.erreur;
        }
})
})


// getArticles();