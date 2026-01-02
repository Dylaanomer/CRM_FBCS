let useCode = null;

/**
 * do this when the document is ready
 */
$(document).ready(async function() {
  try {
    await getCodeTypesLeft()
    await getCodesLeftForType()
  } catch (err) {
    console.error(err)
  }
  setInterval(getCodesLeftForType, 15000);
})

/**
 * handler changing the product we want to use a code of
 */
$(document).on('change', '#usecodetype', function() {
  gegevens.type = $(this).val();
  getCodesLeftForType()
})

/**
 * handler for clicking the gebruik code button
 * retrieve a code and use it's information to render the popup
 */
$(document).on('click', '#usecode', function() {
  getUsableCode()
  .then(result => {
    showPopup = true;
    useCode = result.code;
    renderUseCode(result);
  })
  .catch(err => {
    console.error(err);
  })
})

/**
 * Render the popup to use a code
 * 
 * @param {Object} info 
 */
let renderUseCode = info => {
  if (showPopup) {
    let form = `
    <div id="close"><span role="img" aria-label="close">&#x2716;</span></div>
      <div class="popuptitle">Gebruik Code</div>
      <div class="options">
        <p>Code</p>
        <input type="text" name="code" class="gegevensinput" value="${info.code.slice(0, -2)}" readonly>
        <p>Product</p>
        <input type="text" name="type" class="gegevensinput" value="${info.type}" readonly>
        <p>Ingekocht</p> 	
        <input type="date" name="datein" class="gegevensinput" value="${info.datein}" readonly>
      </div>
      <div class="gegevens">
        <p> Naam </p>
        <select name="aanhef" class="gegevensinput">
          <option value="Fam.">Fam.</option>
          <option value="Dhr.">Dhr.</option>
          <option value="Mevr.">Mevr.</option>
          <option value="Bedrijf">Bedrijf</option>
        </select>
        <div/>
        <input type="text" name="naam" class="gegevensinput" placeholder="Klant"">
        <p> PC </p>
        <select name="pctype" class="gegevensinput"> 
          <option value="Laptop">Laptop</option>
          <option value="Kast">Kast</option>
          <option value="AIO">AIO</option>
          </select>
        <div/>
        <input type="text" name="pc" class="gegevensinput" placeholder="PC""> 
      </div>
      <div class="options">
        <p>Gebruikt</p> 	
        <input type="date" name="dateout" class="gegevensinput" value="${today}">
        <p>Initiaal</p>
        <select name="initiaal" class="gegevensinput"> 
          ` + userOptions + `
        </select>
      </div>

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
      </div>


      <div class="buttons">
        <button id="opslaan">Opslaan</button>
      </div>`

    // write the form to the html of the popup
    $('#popup').html(form);

    gegevens = {
      type: info.type,
      datein: info.datein,
      aanhef: "Fam.",
      naam: new String,
      pctype: "Laptop",
      pc: new String,
      dateout: today,
      initiaal: "FB",
      winver: false,
      regedit: false,
      antivirus: false,
      office: false,
      herstelpunt: false,
      CCleanerMBAMKRVTAdwCleaner: false,
      energiebeheer: false,
      winactivated: false,
      avastinstellingen: false,
      Schijfopslag: false,
      partitiesnaamSDDHDD: false,
      openshell: false,
      FBCSSupremobureablad: false,
      Updates: false,
      Wifi6Settings: false,
      DeliveryOptimization: false,
      VeamAgent: false,
      SchijfopruimingUitgevoerd: false,
      FBCSOpstartMap: false,
      ChromeFirefoxEdge: false,
      CoolerCleaning: false
    }

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
 * request a code for the product set in gegevens.type
 */
let getUsableCode = () => {
  return new Promise((resolve, reject) => {
    let data = 'type='+encodeURIComponent($('#usecodetype').val());
    if (useCode) data += '&code='+encodeURIComponent(useCode);

    jQuery.ajax({
      url: "ajax/get-usable-code.php",
      data: data,
      type: "GET",
      success:function(response){
        data = JSON.parse(response)
        if (data.error) reject(data.error);

        useCode = null; // reset the useCode variable, this will be set after this function or the window is closing
        
        resolve(data)
      },
      error: function(err) { reject(err) }
    })
  })
}

const getCodeTypesLeft = () => {
  return new Promise((resolve, reject) => {
    jQuery.ajax({
      url: "ajax/get-types.php",
      type: "GET",
      success:function(response){
        data = JSON.parse(response)
        if (data.error) return console.error(data.error)

        let selectHtml = '';
        data.forEach(type => { selectHtml += `<option value="${type}"> ${type} </option>` })

        $('#usecodetype').html(selectHtml)

        return resolve()
      },
      error:function(err) { console.error(err); return resolve() }
    })
  })
}

/**
 * request the amount of codes left for the product set in gegevens.type
 */
let getCodesLeftForType = () => {
  return new Promise((resolve, reject) => {
    if (showPopup) return;
    jQuery.ajax({
      url: "ajax/get-codes-left.php",
      data:'type='+encodeURIComponent($('#usecodetype').val()),
      type: "GET",
      success:function(response){
        data = JSON.parse(response)
        if (data.error) return console.error(data.error)
        
        $('#codesleft').html("x"+data.aantal);

        return resolve()
      },
      error:function(err) { console.error(err); return resolve() }
    })
  })
}