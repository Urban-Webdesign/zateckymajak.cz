import "@theme/front/init.scss";

import "bootstrap/js/dist/dropdown";
import "bootstrap/js/dist/collapse";

import "lightbox2/dist/css/lightbox.css";
// eslint-disable-next-line no-unused-vars
import lightbox from "lightbox2/dist/js/lightbox";

import Nette from "@/front/netteForms";
Nette.initOnLoad();
window.Nette = Nette;

document.addEventListener("DOMContentLoaded", () => {

});
