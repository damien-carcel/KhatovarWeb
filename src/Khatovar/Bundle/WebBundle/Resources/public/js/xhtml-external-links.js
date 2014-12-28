// Script written by Drew Noakes -- http://drewnoakes.com
// 11 Dec 2006

function initLinks() {
  for (i in document.links) {
    link = document.links[i];
    if (link.rel && link.rel.indexOf('external')!=-1) {
      link.onclick = onExternalLinkActivate;
      link.onkeypress = onExternalLinkActivate;
    }
  }
}

function onExternalLinkActivate() {
  window.open(this.href);
  return false;
}

window.onload = initLinks;
