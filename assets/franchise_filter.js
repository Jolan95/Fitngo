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
    
            $.ajax({
                type: "GET",
                url: url,
                success: function(data){
                    const content = document.querySelector("#content");
                    content.innerHTML = data.content
                },
            })

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
            $.ajax({
                type: "GET",
                url: url,
                success: function(data){
                    const content = document.querySelector("#content");
                    content.innerHTML = data.content
                },
            })
        })
    })
})