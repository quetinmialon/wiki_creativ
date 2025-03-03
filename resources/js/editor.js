import Quill from "quill";
import "quill/dist/quill.snow.css";

document.addEventListener("DOMContentLoaded", function () {
    const quill = new Quill("#editor", {
        theme: "snow",
        modules: { toolbar: "#toolbar" },
        toolbar: {
            container: "#toolbar",
            handlers: {
                image: function () {
                    uploadImage(quill);
                }
            }
        }
    });

    // Récupère l'input caché
    const hiddenInput = document.getElementById("content");

    // Met à jour le champ caché en temps réel (optionnel)
    quill.on("text-change", function () {
        hiddenInput.value = quill.root.innerHTML;
    });

    // Met à jour le champ caché juste avant soumission
    document.querySelector("form").addEventListener("submit", function (event) {
        hiddenInput.value = quill.root.innerHTML;

        console.log("Contenu envoyé :", hiddenInput.value); // Vérifie dans la console

        if (!hiddenInput.value.trim()) {
            event.preventDefault(); // Empêche l'envoi si le champ est vide
            alert("Le contenu ne peut pas être vide !");
        }
    });
});



/**
 * function that create an new hidden form in html view to handle the storage of images and give back an url to access it.
 */
function uploadImage(quill) {
    const input = document.createElement("input");
    input.type = "file";
    input.accept = "image/*";
    input.onchange = async function () {
        const file = input.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append("image", file);

        try {
            const response = await fetch("/upload-image", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            if (data.url) {
                const range = quill.getSelection();
                quill.insertEmbed(range.index, "image", data.url);
            }
        } catch (error) {
            console.error("Erreur d'upload :", error);
        }
    };
    input.click();
}
