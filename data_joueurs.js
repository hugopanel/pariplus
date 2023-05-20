// Fonction pour supprimer les lettres avant le nom de l'équipe
function removeLettersFromTeamName(teamName) {
  const spaceIndex = teamName.indexOf(' ');
  if (spaceIndex !== -1) {
    return teamName.substring(spaceIndex + 1);
  }
  return teamName;
}

fetch('csv/info_joueur.csv')
  .then(response => response.text())
  .then(csvData => {
    const rows = csvData.split('\n');
    const headers = rows[0].split(',');

    for (let i = 1; i < rows.length; i++) {
      const row = rows[i].split(',');
      const tuple = {};


      const teamName = removeLettersFromTeamName(row[2]);

      for (let j = 0; j < headers.length; j++) {
        tuple[headers[j]] = j === 2 ? teamName : row[j];
      }

      tuples.push(tuple);
    }

    displayTuples();
  })
  .catch(error => {
    console.error('Une erreur s\'est produite lors de la récupération du fichier CSV des joueurs:', error);
  });

// Récupérer le fichier CSV des équipes
fetch('csv/info_equipe.csv')
  .then(response => response.text())
  .then(csvData => {
    // Convertir le CSV en tableau de tuples
    const rows = csvData.split('\n');
    const headers = rows[0].split(',');

    // Parcourir chaque ligne du CSV à partir de la deuxième ligne
    for (let i = 1; i < rows.length; i++) {
      const row = rows[i].split(',');
      const tuple = {};

      const teamName = removeLettersFromTeamName(row[0]);

      for (let j = 0; j < headers.length; j++) {
        tuple[headers[j]] = j === 0 ? teamName : row[j];
      }

      equipeTuples.push(tuple);
    }

    // Afficher les premiers tuples d'équipes
    displayEquipeTuples();
  })
  .catch(error => {
    console.error('Une erreur s\'est produite lors de la récupération du fichier CSV des équipes:', error);
  });






let tuples = []; 
let currentTuplesIndex = 0; 


function displayTuples() {
  const predictionElement = document.getElementById('prediction-joueur');

  for (let i = currentTuplesIndex; i < currentTuplesIndex + 12; i++) {
    if (i >= tuples.length) {
      break; 
    }

    const tuple = tuples[i];
    const tupleHTML = `    
    <div class="card" style="width: 17rem;">
        <div class="card-body">
            <h5 class="card-title">${tuple['Joueur']}</h5>
            <p class="card-subtitle mb-2 text-body-secondary">Joueur de football</p>
            <p class="card-text">
                Equipe : ${tuple['Equipe']}<br>
                Buts : ${tuple['Buts?']}<br>
                Passes décisives : ${tuple['PDP']}<br>
                Pénaltys marqués : ${tuple['PénM']}</p>
        </div>
    </div>
  `;
    predictionElement.innerHTML += tupleHTML;
  }

  currentTuplesIndex += 6;

  const loadMoreBtn = document.getElementById('loadMoreBtn');
  if (currentTuplesIndex >= tuples.length) {
    loadMoreBtn.style.display = 'none';
  }
}

// Récupérer le fichier CSV
fetch('csv/info_joueur.csv')
  .then(response => response.text())
  .then(csvData => {
    const rows = csvData.split('\n');
    const headers = rows[0].split(',');

    for (let i = 1; i < rows.length; i++) {
      const row = rows[i].split(',');
      const tuple = {};

      for (let j = 0; j < headers.length; j++) {
        tuple[headers[j]] = row[j];
      }

      tuples.push(tuple);
    }

    displayTuples();
  })
  .catch(error => {
    console.error('Une erreur s\'est produite lors de la récupération du fichier CSV:', error);
  });

const loadMoreBtn = document.getElementById('loadMoreJoueurBtn');
loadMoreBtn.addEventListener('click', displayTuples);


let equipeTuples = []; 
let currentEquipeIndex = 0; 

function displayEquipeTuples() {
  const equipeElement = document.getElementById('stat-equipe');

  for (let i = currentEquipeIndex; i < currentEquipeIndex + 6; i++) {
    if (i >= equipeTuples.length) {
      break; 
    }

    const tuple = equipeTuples[i];
    const tupleHTML = `
      <div class="card" style="width: 17rem;">
        <div class="card-body">
          <h5 class="card-title">${tuple['Equipe']}</h5>
          <p class="card-subtitle mb-2 text-body-secondary">Equipe de football</p>
          <p class="card-text">
            Nombre de joueurs : ${tuple['# JC']}<br>
            Matchs joués : ${tuple['MJ']}<br>
            Buts : ${tuple['ButsP']}</p>
        </div>
      </div>
    `;
    equipeElement.innerHTML += tupleHTML;
  }

  currentEquipeIndex += 6; 

  const loadMoreEquipeBtn = document.getElementById('loadMoreEquipeBtn');
  if (currentEquipeIndex >= equipeTuples.length) {
    loadMoreEquipeBtn.style.display = 'none'; 
  }
}


fetch('csv/info_equipe.csv')
  .then(response => response.text())
  .then(csvData => {
  
    const rows = csvData.split('\n');
    const headers = rows[0].split(',');

    for (let i = 1; i < rows.length; i++) {
      const row = rows[i].split(',');
      const tuple = {};

      for (let j = 0; j < headers.length; j++) {
        tuple[headers[j]] = row[j];
      }

      equipeTuples.push(tuple);
    }

    displayEquipeTuples();
  })
  .catch(error => {
    console.error('Une erreur s\'est produite lors de la récupération du fichier CSV d\'équipe:', error);
  });

const loadMoreEquipeBtn = document.getElementById('loadMoreEquipeBtn');
loadMoreEquipeBtn.addEventListener('click', displayEquipeTuples);

