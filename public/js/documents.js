window.onload = () => {
    //select delete button
    let links = document.querySelectorAll("[data-delete]");
    for (link of links) {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            if (confirm("Voulez-vous vraiment supprimer ce document?")) {
                // Ajax request to href with DELETE method
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ _token: this.dataset.token }),
                })
                    .then(
                        // Get JSON response
                        (response) => response.json()
                    )
                    .then((data) => {
                        if (data.success) this.parentElement.remove();
                        else alert(data.error);
                    })
                    .catch((e) => alert(e));
            }
        });
    }
};
