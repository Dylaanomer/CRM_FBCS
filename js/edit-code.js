let code = null;

let showPopup = false;        // general Boolean to indicate wether a popup should be shown
let hoverCodeColumn = false;  // Boolean to know if the mouse is hovering above a code row

let gegevens = {
  type: 'Avast Pro',
  datein: new Date(),
  aanhef: new String(),
  naam: new String(),
  pctype: new String(),
  pc: new String(),
  dateout: new Date(),
  initiaal: new String(),
  initiaalCode: new String(),
  aantal: new Number(),
  ongeldig: new Boolean()
}
/* vgm is dit bullshit
$(document).ready(function() {
  renderOptions();
})
*/
/**
 * the folowing two function prevent the popup from opening when clicking the code column in the table
 */
$(document).on('mouseenter', '#codes div:first-child', function() {
  hoverCodeColumn = true;
})

$(document).on('mouseleave', '#codes div:first-child', function () {
  hoverCodeColumn = false;
})

/**
 * when clicking on a code row open the code options
 * this should be prevented when the mouse is on the codes div
 */
$(document).on('click', '#codes div', function() {
  if ($(this).attr('id') && !hoverCodeColumn) { // open if 'this' has a id and mouse is not over code div
    code = $(this).attr('id');
    showPopup = true;
    
    getCodeInfo()
    .then(result => {
      if (result.editing === "1") { // do not continue if this product code is already being edited
        alert('Deze code wordt al bewerkt probeer het later opnieuw')
        return;
      }

      gegevens = {
        type: result.type,
        datein: result.datein,
        aanhef: result.aanhef,
        naam: result.naam,
        pctype: result.pctype,
        pc: result.pc,
        dateout: result.dateout,
        initiaal: result.initiaal,
        initiaalCode: result.initiaalCode,
        aantal: result.aantal,
        ongeldig: result.ongeldig
      }

      toggleEditing()
      .then(() => {
        renderOptions();
      })
      .catch(err => {
        console.error(err)
      })
    })
    .catch(err => {
      console.error(err)
    })
  }
})

/**
 * close a popup after clicking the cross
 */
$(document).on('click', '#close', function() {
  showPopup = false;
  toggleEditing()
  .then(() => {
    code = null;
    renderOptions();
    updateCodes();
  })
})

/**
 * save the edited code to database
 */
$(document).on('click', '#opslaan', function() {
  saveCode()
  .then(() => {
    showPopup = false;
    useCode = null;
    toggleEditing()
    .then(() => {
      code = null;
      renderOptions();
      updateCodes();
    })
  })
  .catch(err => {
    console.error(err);
  })
})

/**
 * set the code to invalid
 */
$(document).on('click', '#ongeldig', function() {
  toggleOngeldig()
  .then(() => {
    showPopup = false;
    toggleEditing()
    .then(() => {
      code = null;
      renderOptions();
      updateCodes();
      getCodesLeftForType();
    })
  })
  .catch(err => {
    console.error(err);
  })
})

/**
 * remove the customer from the selected code
 */
$(document).on('click', '#verwijder', function() {
  deleteCode()
  .then(() => {
    showPopup = false;
    toggleEditing()
    .then(() => {
      code = null;
      renderOptions();
      updateCodes();
      getCodesLeftForType();
    })
  })
  .catch(err => {
    console.error(err);
  })
})

/**
 * edit the code it self I.E. aantal and initiaal
 */
$(document).on('click', '#editcode', function() {
  let form = `<div id="close"><span role="img" aria-label="close">&#x2716;</span></div>
              <div class="popuptitle">Code Bewerken</div>
              <div class="options">
                <p>Code</p>
                <input type="text" name="code" id="editcode" value="${code.slice(0, -2)}" readonly>
                <p>Product</p>
                <input type="text" name="type" class="gegevensinput" value="${gegevens.type}">
                <p>Ingekocht</p> 	
                <input type="date" name="datein" class="gegevensinput" value="${gegevens.datein}">
                <p>Aantal</p> 	
                <input type="number" name="aantal" class="gegevensinput" value="${gegevens.aantal}"/>
                <p>Initiaal</p>
                <select name="initiaalCode" class="gegevensinput"> 
                  ` + userOptions + `
                </select>
              </div>
              <div class="buttons">
                <button id="terug">Terug</button>
                <button id="ongeldig">${gegevens.ongeldig === '1' ? 'Geldig' : 'Ongeldig'}</button>
              </div>`;

  // write the form to the html of the popup
  $('#popup').html(form);

  $(`select[name="initiaalCode"] option[value="${gegevens.initiaalCode}"]`).attr('selected', true);

  gegevens.ongeldig === "1" ? $('#ongeldig').addClass('green-bg') : $('#ongeldig').addClass('red-bg');
})

/**
 * go back to rendering the gegevens options
 */
$(document).on('click', '#terug', function() {
  renderOptions();
})

$(document).on('input', '.gegevensinput', function() {
  let key = $(this).attr('name');
  let value = $(this).val();
  
  gegevens[key] = value;
})

/**
 * set editing column to false in database if still editing but the window closes for any reason
 */
$(window).bind("beforeunload", function() { 
  toggleEditing();
})

let renderOptions = () => {
  if (showPopup && code) {
    let form = `<div id="close"><span role="img" aria-label="close">&#x2716;</span></div>
                <div class="popuptitle">Code Bewerken</div>
                <div class="options">
                  <p>Code</p>
                  <input type="text" name="code" id="editcode" value="${code.slice(0, -2)}" readonly>
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
                  <input type="text" name="naam" class="gegevensinput" placeholder="Klant" value="${gegevens.naam}">
                  <p> PC </p>
                  <select name="pctype" class="gegevensinput"> 
                    <option value="Laptop">Laptop</option>
                    <option value="Kast">Kast</option>
                    <option value="AIO">AIO</option>
                  </select>
                  <div/>
                  <input type="text" name="pc" class="gegevensinput" placeholder="PC" value="${gegevens.pc}"> 
                </div>
                <div class="options">
                  <p>Gebruikt</p> 	
                  <input type="date" name="dateout" class="gegevensinput" value="${gegevens.dateout}">
                  <p>Initiaal</p>
                  <select name="initiaal" class="gegevensinput"> 
                    ` + userOptions + `
                  </select>
                </div>
                <div class="buttons">
                  <button id="opslaan">Opslaan</button>
                  <button id="verwijder" class="red-bg">Verwijder</button>
                </div>`;

    // write the form to the html of the popup
    $('#popup').html(form);        

    // select the options from the resulst
    $(`select[name="aanhef"] option[value="${gegevens.aanhef}"]`).attr('selected', true);
    $(`select[name="pctype"] option[value="${gegevens.pctype}"]`).attr('selected', true);
    $(`select[name="initiaal"] option[value="${gegevens.initiaal}"]`).attr('selected', true);

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
 * request all info about the code
 */
let getCodeInfo = () => {
  return new Promise((resolve, reject) => {
    jQuery.ajax({
      url: "ajax/full-info-code.php",
      data:'code='+encodeURIComponent(code),
      type: "GET",
    success:function(response){
      data = JSON.parse(response)
      if (data.error) reject(data.error);
      
      resolve(data)
    },
    error:function(err){
      reject(err)
    }
    })
  })
}

/**
 * store code info to database
 */
let saveCode = () => {
  return new Promise((resolve, reject) => {
    let thiscode = code ? code : useCode;
    jQuery.ajax({
      url: "ajax/save-code.php",
      data:'code='+encodeURIComponent(thiscode)+
            '&type='+encodeURIComponent(gegevens.type)+
            '&datein='+encodeURIComponent(gegevens.datein)+
            '&aanhef='+encodeURIComponent(gegevens.aanhef)+
            '&naam='+encodeURIComponent(gegevens.naam)+
            '&pctype='+encodeURIComponent(gegevens.pctype)+
            '&pc='+encodeURIComponent(gegevens.pc)+
            '&dateout='+encodeURIComponent(gegevens.dateout)+
            '&initiaal='+encodeURIComponent(gegevens.initiaal),
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

/**
 * Update the editing column for the clicked code, preventing other users from changing the code at the same time
 */
let toggleEditing = () => {
  return new Promise((resolve, reject) => {
    if (!code && !useCode) resolve();
    if (code) {
      jQuery.ajax({
        url: "ajax/toggle-editing.php",
        data:'code='+encodeURIComponent(code),
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
    }
    if (useCode) {
      getUsableCode()
      .then(resolve())
      .catch(err => reject(err))
    }
  })
}

/**
 * change code:
 * geldig -> ongeldig || geldig -> ongeldig
 */
let toggleOngeldig = () => {
  return new Promise((resolve, reject) => {
    jQuery.ajax({
      url: "ajax/save-code.php",
      data:'code='+encodeURIComponent(code)+
            '&ongeldig=1',
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

/**
 * delete customer from this code 'freeing' a spot
 */
let deleteCode = () => {
  return new Promise((resolve, reject) => {
    jQuery.ajax({
      url: "ajax/save-code.php",
      data:'code='+encodeURIComponent(code)+
            '&verwijder=1',
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