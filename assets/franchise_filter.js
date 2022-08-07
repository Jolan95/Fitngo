$("document").ready(()=>{
    const filtersForm = document.querySelector("#filter")

    document.querySelectorAll("#filter input").forEach((input)=>{
        
        input.addEventListener("change", ()=>{
            const form = new FormData(filtersForm);
            

            //queryString
            const param = new URLSearchParams();

            form.forEach((value, key) => {
                param.append(key, value)
            })
            

            //url
            let url = new URL(window.location.href)
            url = url.pathname + "?" +param.toString()+"&ajax=1";

            // // On lance la requête ajax
            // fetch(url.pathname + "?" + param.toString() + "&ajax=1", {
            //     headers: {
            //         "X-Requested-With": "XMLHttpRequest"
            //     }
            // }).then(response => 
            //     response.json()
            // ).catch((e)=> alert(e));
            $.ajax({
                type: "GET",
                url: url,
                success: function(data){
                    const content = document.querySelector("#content");
                    content.innerHTML = data.content
                },
            })
            // .then(response => response.json())
            // .then(data =>{
            // })

        })
    })
    document.querySelectorAll("#filter input").forEach((input)=>{
        
        input.addEventListener("keyup", ()=>{
            const form = new FormData(filtersForm);
            

            //queryString
            const param = new URLSearchParams();

            form.forEach((value, key) => {
                param.append(key, value)
            })
            

            //url
            let url = new URL(window.location.href)
            url = url.pathname + "?" +param.toString()+"&ajax=1";

            // // On lance la requête ajax
            // fetch(url.pathname + "?" + param.toString() + "&ajax=1", {
            //     headers: {
            //         "X-Requested-With": "XMLHttpRequest"
            //     }
            // }).then(response => 
            //     response.json()
            // ).catch((e)=> alert(e));
            $.ajax({
                type: "GET",
                url: url,
                success: function(response) {
                    console.log(response);
                }
            });
        })
    })
})