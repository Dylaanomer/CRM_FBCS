let newCode = {
  code: new String,
  type: new String,
  datein: new Date,
  aantal: new Number,
  initiaal: new String
}

let today = new Date().toISOString().slice(0,10);

/**
 * handler to start rendering the new code popup
 */
$(document).on('click', '#newcode', function() {
  showPopup = true;
  renderNewCode();
})

/**
 * handler for adding the code to the database
 */
$(document).on('click', '#toevoegen', function() {
  saveNewCode()
  .then(() => {
    showPopup = false;
    renderNewCode();
  })
  .catch(err => {
    console.error(err);
  })
})

/**
 * change the object newCode based on the name attribute of a input with the class newcodeinput
 */
$(document).on('input', '.newcodeinput', function() {
  let key = $(this).attr('name');
  let value = $(this).val();
  
  newCode[key] = value;
})

/**
 * render the popup for a new code
 */
let renderNewCode = () => {
  if (showPopup) {
    let form = `<div id="close"><span role="img" aria-label="close">&#x2716;</span></div>
                <div class="popuptitle">Nieuwe Code</div>
                <div class="options">
                  <p>Code</p>
                  <input type="text" name="code" class="newcodeinput" placeholder="Code">
                  <p>Product</p>
                  <input type="text" name="type" list="producttype" class="newcodeinput">
                  <datalist id="producttype" >
                    <option value="Avast Pro"> Avast Pro </option>
                    <option value="Office H&S 2019"> Office H&S 2019 </option>
                    <option value="Office H&B 2019"> Office H&B 2019 </option>
                    <option value="Office Pro Plus 2019"> Office Pro Plus 2019 </option>
                    <option value="Office H&S 2016"> Office H&S 2016 </option>
                    <option value="Office H&B 2016"> Office H&B 2016 </option>
                    <option value="Office Pro Plus 2016"> Office Pro Plus 2016 </option>
                    <option value="Office H&S 2013"> Office H&S 2013 </option>
                    <option value="Office H&B 2013"> Office H&B 2013 </option>
                    <option value="Office Pro Plus 2013"> Office Pro Plus 2013 </option>
                    <option value="Office 365 1 PC 1 jaar"> Office 365 1 PC 1 jaar </option>
                  </datalist>                  <p>Ingekocht</p> 	
                  <input type="date" name="datein" class="newcodeinput" value="${today}"/>
                  <p>Aantal</p> 	
                  <input type="number" name="aantal" class="newcodeinput" value="1"/>
                  <p>Initiaal</p>
                  <select name="initiaal" class="newcodeinput"> 
                    ` + userOptions + `
                  </select>
                </div>
                <div class="buttons">
                  <button id="toevoegen">Toevoegen</button>
                </div>`;

    newCode = {
      code: new String,
      type: 'Avast Pro',
      datein: today,
      aantal: 1,
      initiaal: 'FB'
    }

    // write the form to the html of the popup
    $('#popup').html(form);     

    // show the popup
    $('#popupBG').css('display','block')
    $('body').css('overflow', 'hidden')
  } else {
    $('#popup').html('')
    $('#popupBG').css('display','none')
    $('body').css('overflow', 'unset')
  }
}

/**
 * store the new code to the database
 */
let saveNewCode = () => {
  return new Promise((resolve, reject) => {
    jQuery.ajax({
      url: "ajax/new-code.php",
      data:'code='+encodeURIComponent(newCode.code)+
            '&type='+encodeURIComponent(newCode.type)+
            '&datein='+encodeURIComponent(newCode.datein)+
            '&aantal='+encodeURIComponent(newCode.aantal)+
            '&initiaal='+encodeURIComponent(newCode.initiaal),
      type: "POST",
    success:function(response){
      data = JSON.parse(response)
      if (data.error) reject(data.error);
      
      resolve()
    },
    error:function(err){
      reject(err)
    }
    })
  })
}