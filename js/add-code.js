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
                <div class="popuptitle">Checklist nieuwe apparaten</div>
                <div class="options">
                  <div class="checkboxes">
        <label><input type="checkbox" name="winver"> Windows versie geinstalleerd</label>
        <label><input type="checkbox" name="regedit"> Regedit uitgevoerd</label>
        <label><input type="checkbox" name="antivirus"> Antivirus geinstalleerd</label>
        <label><input type="checkbox" name="office"> Office geinstalleerd</label>
        <label><input type="checkbox" name="herstelpunt"> Herstelpunt aangemaakt</label>
        <label><input type="checkbox" name="CCleanerMBAMKRVTAdwCleaner"> CCleaner, MBAM, KRVT, AdwCleaner geinstalleerd</label>
        <label><input type="checkbox" name="energiebeheer"> Energiebeheer ingesteld</label>
        <label><input type="checkbox" name="winactivated"> Windows geactiveerd</label>
        <label><input type="checkbox" name="avastinstellingen"> Avast instellingen gedaan</label>
        <label><input type="checkbox" name="Schijfopslag"> Schijfopslag geoptimaliseerd</label>
        <label><input type="checkbox" name="partitiesnaamSDDHDD"> Partities hernoemd (SSD/HDD)</label>
        <label><input type="checkbox" name="openshell"> Open-Shell geinstalleerd</label>
        <label><input type="checkbox" name="FBCSSupremobureablad"> FBCS Supremobureablad ingesteld</label>
        <label><input type="checkbox" name="Updates"> Alle updates uitgevoerd</label>
        <label><input type="checkbox" name="Wifi6Settings"> Wifi 6 instellingen gedaan</label>
        <label><input type="checkbox" name="DeliveryOptimization"> Delivery Optimization ingesteld</label>
        <label><input type="checkbox" name="VeamAgent"> Veam Agent geinstalleerd</label>
        <label><input type="checkbox" name="SchijfopruimingUitgevoerd"> Schijfopruiming uitgevoerd</label>
        <label><input type="checkbox" name="FBCSOpstartMap"> FBCS Opstart Map ingesteld</label>
        <label><input type="checkbox" name="ChromeFirefoxEdge"> Chrome, Firefox, Edge geinstalleerd</label>
        <label><input type="checkbox" name="CoolerCleaning"> Koeler schoongemaakt</label>

         <p>Initiaal</p>
                  <select name="initiaal" class="newcodeinput"> 
                    ` + userOptions + `
                  </select>

         <div class="notities">
                  <p> Notities </p>
                  <textarea name="Notities" class="newcodeinput" placeholder="Notities"></textarea>
      
                </div>         
      </div>
              
                
      
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