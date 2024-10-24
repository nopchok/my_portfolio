function get_review(activity_id, k_currency, k_lang, page, limit){
    return new Promise((resolve, reject)=>{
        //           https://www.klook.com/v1/experiencesrv/activity/component_service/activity_reviews_list?k_lang=th_TH      &k_currency=THB            &activity_id=375             &page=2        &limit=8         &star_num=1,2,3,4,5&lang=&sort_type=1&only_image=false
        const url = `https://www.klook.com/v1/experiencesrv/activity/component_service/activity_reviews_list?k_lang=${ k_lang }&k_currency=${ k_currency }&activity_id=${ activity_id }&page=${ page }&limit=${ limit }&star_num=&lang=&sort_type=1&only_image=false`;
        fetch(url, {
            "headers": {
            },
            "referrer": "https://www.klook.com/activity/365-safari-world-bangkok/",
            "referrerPolicy": "strict-origin-when-cross-origin",
            "body": null,
            "method": "GET",
            "mode": "cors",
            "credentials": "include"
        }).then(j=>j.json()).then(resolve).catch(e=>reject);
    })
}

async function run(data){
    const activity_id = location.href.split('activity/')[1].split('-')[0];
    const k_currency = data.Language;
    const k_lang = data.Currency;

    let page = 1
    const limit = 100;
    
    let all_review = [];
    while(true){
        try{
            let review = await get_review(activity_id, k_currency, k_lang, page, limit);
            if( review.success ){
                if( review.result.item.length == 0 ) break;
                
                all_review = all_review.concat(review.result.item);
            }
            page++;
        }catch(e){
            console.log(e);
            break;
        }
    }
    
    let package_count = all_review.map(j=>{return j?(j.package_name||''):'';}).reduce(function(obj, b) {
        obj[b] = ++obj[b] || 1;
        return obj;
    }, {});


    // object sorted
    const obj = Object.entries(package_count).sort((a, b) => b[1] - a[1]);
    const sorted_package = Object.fromEntries(obj);
    

    // DOM
    let node, textnode;
    const div = document.createElement("div");
    div.style.marginTop = '20px';
    const h4 = document.createElement("h4");
    textnode = document.createTextNode('Top package from lasted 200 user\'s review');
    h4.appendChild(textnode);
    div.appendChild(h4);

    const ul = document.createElement("ul");
    ul.style.listStyleType = 'disclosure-closed';
    for( let k in sorted_package ){
        node = document.createElement("li");
        let v = sorted_package[k];
        if( v > 1 ){
            textnode = document.createTextNode(v + ' : ' + k);
            node.appendChild(textnode);
            ul.appendChild(node);
        }
    }
    div.appendChild(ul);
    document.querySelector('[class="tab-content"]').appendChild(div);
}


function wait_dataLayer(){
    try{
        console.log('wait_dataLayer');

        // scrape
        if( document.querySelectorAll('[class="tab-content"]').length == 0 ) throw 'div not found';
        let data = JSON.parse(document.querySelectorAll('script[data-n-head="ssr"]')[1].innerText.match('\{.*\}')[0])
        run(data);
    }catch(e){
        console.log(e);
        setTimeout(wait_dataLayer, 1000);
    }
}

wait_dataLayer();