const btnEdit = document.getElementById("btnEdit");
const editor = document.getElementById("editor");

let editing = false;

btnEdit.addEventListener("click", async function () {

    const id = new URLSearchParams(window.location.search).get("id");

    // =========================
    // MODE EDIT
    // =========================
    if (!editing) {

        editing = true;

        editor.contentEditable = true;

        editor.focus();

        btnEdit.innerHTML =
            '<i class="bi bi-floppy"></i> Simpan';

        return;
    }

    // =========================
    // MODE SIMPAN
    // =========================

    const html = editor.innerHTML;

    btnEdit.disabled = true;

    btnEdit.innerHTML =
        '<i class="bi bi-hourglass-split"></i> Menyimpan...';

    try {

        const response = await fetch("save.php", {

            method: "POST",

            headers: {
                "Content-Type":
                    "application/x-www-form-urlencoded"
            },

            body:
                "id=" + encodeURIComponent(id) +
                "&html=" + encodeURIComponent(html)

        });

        const result = await response.json();

        if (result.success) {

            editing = false;

            editor.contentEditable = false;

            btnEdit.innerHTML =
                '<i class="bi bi-pencil-square"></i> Edit';

            alert("✅ Modul berhasil disimpan");

        } else {

            alert(
                "❌ Gagal menyimpan: " +
                (result.message || "Terjadi kesalahan.")
            );

            btnEdit.innerHTML =
                '<i class="bi bi-floppy"></i> Simpan';
        }

    } catch (error) {

        console.error(error);

        alert("❌ Terjadi kesalahan saat menyimpan modul.");

        btnEdit.innerHTML =
            '<i class="bi bi-floppy"></i> Simpan';

    }

    btnEdit.disabled = false;

});