/* crunserver.js 
* Info: Fontsize auch PICO nur via JD.dashSetFont() aenderrbarm --pico-font-size nicht mgl.
*
* Loaded: defered
*/

import * as JD from './jodash.js'
import './FileSaver.js' // SaveAs

const _dbg = false // true // - Set to true to get sample data

//--------- globals ------ 
export const VERSION = 'V0.05 / 18.02.2025'
export const COPYRIGHT = '(C)JoEmbedded.de'

document.getElementById("svers").textContent = VERSION


// Testdaten, wie erzeugt vom Converter
if (_dbg) {
    const dbgbut = document.getElementById("debugbutton")
    dbgbut.hidden = false
    dbgbut.addEventListener('click', async () => {
        const tdet = document.getElementById("tdetails").content.cloneNode(true)
        const qdi = JD.prepareCustomDialog("TestDialog", tdet)
        await JD.doCustomDialog(qdi)
    }
    )
}

// Daten als File exportieren, benoetigt FileSaver.js
async function fileExport(string, fname) {
    try {
        const atype = 'text/plain;charset=utf-8'
        const blob = new Blob([string], { type: atype }) // BlobType: MDN-File API
        /*eslint-disable-next-line*/
        saveAs(blob, fname)
    } catch (err) {
        await JD.doDialogOK("ERROR (Export)", err)
    }
}
async function buttonGet(e) { // Klick evtl. aufs Symbol, daher 2 Parents up
    let crun = e.target.parentNode.querySelector(".tcrun")?.textContent
    if (!crun) crun = e.target.parentNode.parentNode.querySelector(".tcrun").textContent
    JD.fetch_get_txt(`./php/cget.php?cf=${crun}`, async (e) => {
        await fileExport(e, crun)
    })
}

async function buttonDetails(e) { // Klick evtl. aufs Symbol, daher 2 Parents up
    let crun = e.target.parentNode.querySelector(".tcrun")?.textContent
    if (!crun) crun = e.target.parentNode.parentNode.querySelector(".tcrun").textContent
    const tdet = document.getElementById("tdetails").content.cloneNode(true)
    const spath = new URL(location).href
    const cruncmd = `.crun ${spath}php/cget.php?cf=${encodeURI(crun)}`
    tdet.querySelector(".spath").textContent = cruncmd
    tdet.querySelector(".tqrimg").src = "./php_qr/ltx_qr.php?text=CMD:" + cruncmd
    tdet.querySelector(".tbcopy").addEventListener('click', async () => {
        try {
            if(!navigator.clipboard) throw new Error("No Clipboard!") // Only for Secure Servers?
            navigator.clipboard.writeText(cruncmd)
            await JD.doDialogOK("Copied to Clipboard:", "<br>'" + cruncmd + "'<br><br>")
        } catch (err) {
            await JD.doDialogOK("ERROR", "<br>'" + err + "'<br><br>")
        }
    })
    const qdi = JD.prepareCustomDialog(`Details '${crun}'`, tdet)
    await JD.doCustomDialog(qdi)
}

//--init defered--
//JD.dashSetFont(0.75)
JD.fetch_get_json('./php/worker.php?cmd=clist', (cres) => {
    const clist = cres.clist
    const slistdiv = document.getElementById("slistdiv")
    slistdiv.innerHTML = null
    clist.forEach((celem) => {
        const clone = document.getElementById("templine").content.cloneNode(true) // DeepClone
        clone.querySelector(".tcrun").textContent = celem
        clone.querySelector(".tcget").addEventListener('click', buttonGet)
        clone.querySelector(".tcdetails").addEventListener('click', buttonDetails)
        slistdiv.appendChild(clone)
    });
}, (err) => JD.doDialogOK("ERROR", err))
console.log("crunserver.js init, Version:", VERSION)
