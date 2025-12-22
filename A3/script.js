document.getElementById("red").addEventListener("click", function(){
    if(this.style.backgroundColor == "red"){
        this.style.backgroundColor = "rgb(240, 240, 240)";
        this.style.color = "black";
    } else{
        this.style.backgroundColor = "red";
        this.style.color = "white";
    }
});

document.getElementById("blue").addEventListener("click", function(){
    if(this.style.backgroundColor == "blue"){
        this.style.backgroundColor = "rgb(240, 240, 240)";
        this.style.color = "black";
    } else{
        this.style.backgroundColor = "blue";
        this.style.color = "white";
    }
});

document.getElementById("green").addEventListener("click", function(){
    if(this.style.backgroundColor == "green"){
        this.style.backgroundColor = "rgb(240, 240, 240)";
        this.style.color = "black";
    } else{
        this.style.backgroundColor = "green";
        this.style.color = "white";
    }
});

document.getElementById("yellow").addEventListener("click", function(){
    if(this.style.backgroundColor == "yellow"){
        this.style.backgroundColor = "rgb(240, 240, 240)";
    } else{
        this.style.backgroundColor = "yellow";
    }
});

const nameInput = document.getElementById("name");
const btn = document.querySelector('button');

nameInput.addEventListener('input', function(){
    if(nameInput.value.trim() != ""){
        btn.disabled = false;
    } else{
        btn.disabled = true;
    }
});

const greetLine = document.firstElementChild.lastElementChild.firstElementChild;

btn.onclick = function(){
    greetLine.innerHTML = `<p>Hello, ${nameInput.value}</p>`;
};