

function createNotification11(title, content) {
    if (Notification.permission === 'granted') {
      var notification = new Notification(title, {
        body: content
      });
    }
    else if (Notification.permission !== 'denied') {
      Notification.requestPermission(function(permission) {
        if (permission === 'granted') {
          var notification = new Notification(title, {
            body: content
          });
        }
      });
    }
  }


  function showNotification(title, content) {
    if (Notification.permission === "granted") {
      var notification = new Notification(title, {
        body: content
      });

      // Ajouter une classe pour la personnalisation
      notification.addEventListener("show", function() {
        document.querySelectorAll(".notification").forEach(function(item) {
          item.classList.add("show");
        });
      });
      
      // Supprimer la classe pour cacher la notification
      notification.addEventListener("click", function() {
        document.querySelectorAll(".notification").forEach(function(item) {
          item.classList.remove("show");
        });
      });
    } else if (Notification.permission !== "denied") {
      Notification.requestPermission().then(function(permission) {
        if (permission === "granted") {
          showNotification();
        }
      });
    }
  }


  function updatePanierIcon() {
    // Récupérer le nombre de produits dans le panier
    $.ajax({
      url: '/nbprod', // la route qui retourne le nombre de produits dans le panier
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        // Mettre à jour le contenu de l'icône du panier
        var icone = document.querySelector('#cart-icon');
        //cartIcon.textContent = response.nbp;
        icone.innerHTML += " (" + response.nbp + ")";      
      }
    });
  }

  function init() {
    updatePanierIcon();
  }
  
  window.onload = init;


