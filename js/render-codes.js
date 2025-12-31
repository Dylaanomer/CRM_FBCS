let showType = 'avast';
let search = new String;
let showOldCodes = false
let oldestCode = new Date()

/**
 * header for codes table
 */
const header = `
  <div class="codes-header">
    <div>Code</div>
    <div class="tabletype">Product</div>
    <div>Klant</div>
    <div>PC</div>
    <div>Datum</div>
  </div>`

/**
 * do this when document is ready
 */
$(document).ready(() => {
  updateCodes();
  setInterval(updateCodes, 15000);
})

/**
 * update search variable on input searchbox for codestable
 */
$(document).on('input', '#search-code', function() {
  showOldCodes = false

  search = $(this).val();

  updateCodes();
})

/**
 * change the type of codes we want to see
 */
$(document).on('click', 'input[name=type]', function() {
  if ($(this).val() !== showType) {
    showType = $(this).val();
    updateCodes();
  }
})

$(document).on('click', '#loadmore', function() {
  renderOlderCodes()
  .then(result => {
    let codes = '';

    for(let i = 0; i < result.length; i++) {
      let row = result[i];

      codes += `
        <div id="${row.code}" class="${i % 2 === 0 ? `` : `codes-even`} ${row.ongeldig === '1' ? "red-bg" : ''}">
          <div>${row.code.slice(0, -2)}</div>
          <div class="tabletype">${row.type}</div>
          <div>${row.aanhef} ${row.naam}</div>
          <div>${row.pctype} - ${row.pc}</div>
          <div>${row.dateout}</div>
        </div>`

      // set last code date we can see to prevent pagination using offset
      if (i === result.length - 1) oldestCode = row.updated
    }

    $('#codes').append(codes);
  })
  .catch(err => { console.error(err) })
})

/**
 * request the latest codes
 */
let updateCodes = () => {
  if (showPopup || showOldCodes) return; // do not update the list while the popup is active or if the user wants to see older codes
  let xhr = search.length > 2 ? searchCodes() : getLastUpdatedCodes();

  xhr
  .then(result => {
    let codes = '';

    for(let i = 0; i < result.length; i++) {
      let row = result[i];

      codes += `
        <div id="${row.code}" class="${i % 2 === 0 ? `` : `codes-even`} ${row.ongeldig === '1' ? "red-bg" : ''}">
          <div>${row.code.slice(0, -2)}</div>
          <div class="tabletype">${row.type}</div>
          <div>${row.aanhef} ${row.naam}</div>
          <div>${row.pctype} - ${row.pc}</div>
          <div>${row.dateout}</div>
        </div>`

      // set last code date we can see to prevent pagination using offset
      if (i === result.length - 1) oldestCode = row.updated
    }

    $('#codes').html(header+codes);
  })
  .catch(err => {
    console.error(err);
    $('#codes').html(header+'<div>0 results</div>');
  })
}

/**
 * get the codes last changed 
 */
const getLastUpdatedCodes = () => {
  return new Promise((resolve, reject) => {
    jQuery.ajax({
      url: "ajax/info-codes.php",
      data:'type='+encodeURIComponent(showType),
      type: "GET",
    success:function(response){
      data = JSON.parse(response)
      if (data.error) reject(data.error);

      return resolve(data)
    },
    error:function(err){
      return reject(err)
    }
    })
  })
}

const renderOlderCodes = () => {
  return new Promise((resolve, reject) => {
    showOldCodes = true

    jQuery.ajax({
      url: "ajax/info-codes.php",
      data:'type='+encodeURIComponent(showType)+'&since='+encodeURIComponent(oldestCode),
      type: "GET",
    success:function(response){
      data = JSON.parse(response)
      if (data.error) reject(data.error);

      return resolve(data)
    },
    error:function(err){
      return reject(err)
    }
    })
  })
}

/**
 * search for codes in the database
 * search in code, naam, pc
 */
const searchCodes = () => {
  return new Promise((resolve, reject) => {
    jQuery.ajax({
      url: "ajax/info-codes.php",
      data:'type='+encodeURIComponent(showType)+'&search='+encodeURIComponent(search),
      type: "GET",
    success:function(response){
      data = JSON.parse(response)
      if (data.error) reject(data.error);
      
      return resolve(data)
    },
    error:function(err){
      return reject(err)
    }
    })
  })
}