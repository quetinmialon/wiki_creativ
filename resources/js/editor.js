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

    //get hidden input
    const hiddenInput = document.getElementById("content");

    //update hidden input dynamically
    quill.on("text-change", function () {
        hiddenInput.value = quill.root.innerHTML;
    });

    //handle paste image event
    quill.root.addEventListener("paste", async (event) => {
        const clipboardData = event.clipboardData || window.clipboardData;
        if (clipboardData) {
            const items = clipboardData.items;
            for (const item of items) {
                if (item.type.startsWith("image/")) {
                    const file = item.getAsFile();
                    if (file) {
                        const range = quill.getSelection();
                        const reader = new FileReader();

                        reader.onload = function (e) {
                            const base64Image = e.target.result;
                            uploadPastedImage(file, base64Image, quill);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            }
        }
    });
});

/**
 * upload paste image to store it properly and render the url
 */
async function uploadPastedImage(file, base64Image, quill) {
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
            //find every 64bit images in quill container
            const images = quill.root.querySelectorAll("img");
            images.forEach((img) => {
                if (img.src === base64Image) {
                    img.src = data.url; //change 64bit string to url link
                }
            });

            // update inner html properly
            document.getElementById("content").value = quill.root.innerHTML;
        }
    } catch (error) {
        console.error("Erreur d'upload :", error);
    }
}
