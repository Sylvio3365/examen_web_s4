<link rel="stylesheet" href="public/css/style.css">

<h1>Gestion des types de prêt</h1>

  <div>
    <input type="hidden" id="idtypepret">
    <input type="text" id="nom" placeholder="Nom du type">
    <input type="number" id="taux_annuel" placeholder="Taux annuel (%)" step="0.01">
    <input type="number" id="montant_min" placeholder="Montant minimum">
    <input type="number" id="montant_max" placeholder="Montant maximum">
    <input type="number" id="duree_max" placeholder="Durée max (mois)">
    <input type="number" id="taux_assurance" placeholder="taux_assurance">
    <button onclick="ajouterOuModifier()">Ajouter / Modifier</button>
  </div>

  <table id="table-typeprets">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Taux annuel</th>
        <th>Montant min</th>
        <th>Montant max</th>
        <th>Durée max</th>
        <th>Taux assurance</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <script>
    const apiBase = "<?php echo $apiBase ?>";

    function ajax(method, url, data, callback) {
      const xhr = new XMLHttpRequest();
      xhr.open(method, apiBase + url, true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = () => {
        if (xhr.readyState === 4 && xhr.status === 200) {
          callback(JSON.parse(xhr.responseText));
        }
      };
      xhr.send(data);
    }

    function chargerTypePrets() {
      ajax("GET", "/typeprets", null, (data) => {
        const tbody = document.querySelector("#table-typeprets tbody");
        tbody.innerHTML = "";
        data.forEach(t => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${t.idtypepret}</td>
            <td>${t.nom}</td>
            <td>${t.taux_annuel}</td>
            <td>${t.montant_min}</td>
            <td>${t.montant_max}</td>
            <td>${t.duree_max}</td>
            <td>${t.taux_assurance}</td>
            <td>
              <button onclick='remplirFormulaire(${JSON.stringify(t)})'>✏️</button>
              <button onclick='supprimerTypePret(${t.idtypepret})'>🗑️</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      });
    }

    function ajouterOuModifier() {
      const idtypepret = document.getElementById("idtypepret").value;
      const nom = document.getElementById("nom").value;
      const taux_annuel = document.getElementById("taux_annuel").value;
      const montant_min = document.getElementById("montant_min").value;
      const montant_max = document.getElementById("montant_max").value;
      const duree_max = document.getElementById("duree_max").value;
      const taux_assurance =document.getElementById("taux_assurance").value;

      const data = {
        nom: nom,
        taux_annuel: parseFloat(taux_annuel),
        montant_min: parseFloat(montant_min),
        montant_max: parseFloat(montant_max),
        duree_max: parseFloat(duree_max),
        taux_assurance: parseFloat(taux_assurance)
      };

      const xhr = new XMLHttpRequest();
      xhr.open(idtypepret ? "PUT" : "POST", apiBase + `/typeprets/${idtypepret || ''}`, true);
      xhr.setRequestHeader("Content-Type", "application/json");
      xhr.onreadystatechange = () => {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            resetForm();
            chargerTypePrets();
          } else {
            console.error("Erreur:", xhr.status, xhr.statusText);
          }
        }
      };
      xhr.send(JSON.stringify(data));
    }

    function remplirFormulaire(t) {
      document.getElementById("idtypepret").value = t.idtypepret;
      document.getElementById("nom").value = t.nom;
      document.getElementById("taux_annuel").value = t.taux_annuel;
      document.getElementById("montant_min").value = t.montant_min;
      document.getElementById("montant_max").value = t.montant_max;
      document.getElementById("duree_max").value = t.duree_max;
      document.getElementById("taux_assurance").value = t.taux_assurance;
    }

    function supprimerTypePret(id) {
      if (confirm("Supprimer ce type de prêt ?")) {
        ajax("DELETE", `/typeprets/${id}`, null, () => {
          chargerTypePrets();
        });
      }
    }

    function resetForm() {
      document.getElementById("idtypepret").value = "";
      document.getElementById("nom").value = "";
      document.getElementById("taux_annuel").value = "";
      document.getElementById("montant_min").value = "";
      document.getElementById("montant_max").value = "";
      document.getElementById("duree_max").value = "";
      document.getElementById("taux_assurance").value = "";
    }

    chargerTypePrets();
  </script>
