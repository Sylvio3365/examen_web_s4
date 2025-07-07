<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Gestion des types de prêt</title>
  <link rel="stylesheet" href="public/css/style.css">
</head>


<body>

  <h1>Gestion des types de prêt</h1>

  <div>
    <input type="hidden" id="id">
    <input type="text" id="nom" placeholder="Nom du type">
    <input type="number" id="taux_interet" placeholder="Taux d’intérêt (%)" step="0.01">
    <input type="number" id="duree_mois" placeholder="Durée (mois)">
    <button onclick="ajouterOuModifier()">Ajouter / Modifier</button>
  </div>

  <table id="table-typeprets">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Taux d’intérêt</th>
        <th>Durée (mois)</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <script>
    const apiBase = "http://localhost/examen_web_s4/ws";

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
            <td>${t.id}</td>
            <td>${t.nom}</td>
            <td>${t.taux_interet}</td>
            <td>${t.duree_mois}</td>
            <td>
              <button onclick='remplirFormulaire(${JSON.stringify(t)})'>✏️</button>
              <button onclick='supprimerTypePret(${t.id})'>🗑️</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      });
    }

    function ajouterOuModifier() {
      const id = document.getElementById("id").value;
      const nom = document.getElementById("nom").value;
      const taux_interet = document.getElementById("taux_interet").value;
      const duree_mois = document.getElementById("duree_mois").value;

      const data = `nom=${encodeURIComponent(nom)}&taux_interet=${taux_interet}&duree_mois=${duree_mois}`;

      if (id) {
        ajax("PUT", `/typeprets/${id}`, data, () => {
          resetForm();
          chargerTypePrets();
        });
      } else {
        ajax("POST", "/typeprets", data, () => {
          resetForm();
          chargerTypePrets();
        });
      }
    }

    function remplirFormulaire(t) {
      document.getElementById("id").value = t.id;
      document.getElementById("nom").value = t.nom;
      document.getElementById("taux_interet").value = t.taux_interet;
      document.getElementById("duree_mois").value = t.duree_mois;
    }

    function supprimerTypePret(id) {
      if (confirm("Supprimer ce type de prêt ?")) {
        ajax("DELETE", `/typeprets/${id}`, null, () => {
          chargerTypePrets();
        });
      }
    }

    function resetForm() {
      document.getElementById("id").value = "";
      document.getElementById("nom").value = "";
      document.getElementById("taux_interet").value = "";
      document.getElementById("duree_mois").value = "";
    }

    chargerTypePrets();
  </script>

</body>

</html>
