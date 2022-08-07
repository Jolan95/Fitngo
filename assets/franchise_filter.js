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
            const url = new URL(window.location.href)

            // On lance la requête ajax
            fetch(url.pathname + "?" + param.toString() + "&ajax=1", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            }).then(response => 
                response.json()
            ).catch((e)=> alert(e));
        })
    })
})