let article = document.querySelector('#article');
let error = document.querySelector('#error');
let urlCourante = document.location.href;
const url = 'https://localhost:8000/api/article/get/id'+id;
let id = ;
let json = JSON.stringify({id:id});