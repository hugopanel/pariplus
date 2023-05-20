// Récupérer le fichier CSV des équipes
fetch('csv/info_equipe2.csv')
  .then(response => response.text())
  .then(csvData => {
    const rows = csvData.split('\n');
    const teamOptions1 = document.getElementById('teamSelect1');
    const teamOptions2 = document.getElementById('teamSelect2');

    for (let i = 1; i < rows.length; i++) {
      const row = rows[i].split(',');

      const teamName = row[1];

      const option1 = document.createElement('option');
      option1.textContent = teamName;
      teamOptions1.appendChild(option1);

      const option2 = document.createElement('option');
      option2.textContent = teamName;
      teamOptions2.appendChild(option2);
    }
  })
  .catch(error => {
    console.error('Une erreur s\'est produite lors de la récupération du fichier CSV des équipes:', error);
  });



// Fonction pour afficher les noms des équipes
function displayEquipe(equipe1, equipe2) {
  const outputDiv = document.getElementById('output');
  
  outputDiv.textContent = '';
 
  const equipe1Element = document.createElement('p');
  const equipe2Element = document.createElement('p');
 
  outputDiv.appendChild(equipe1Element);
  outputDiv.appendChild(equipe2Element);
}


// Fonction pour récupérer les noms des équipes sélectionnées
function getEquipe() {
  const teamSelect1 = document.getElementById('teamSelect1');
  const teamSelect2 = document.getElementById('teamSelect2');
  const equipe1 = teamSelect1.options[teamSelect1.selectedIndex].textContent;
  const equipe2 = teamSelect2.options[teamSelect2.selectedIndex].textContent;

  displayEquipe(equipe1, equipe2);
  return {
    equipe1,
    equipe2
  };
}


function rechercheLigneEquipe(nomEquipe) {
  const csvData = 'csv/info_equipe2.csv'; // Chemin vers le fichier CSV

  return new Promise((resolve, reject) => {
    Papa.parse(csvData, {
      header: true,
      download: true,
      complete: (results) => {
        const equipeLigne = results.data.find((row) => row.Equipe === nomEquipe);

        if (equipeLigne) {
          resolve(equipeLigne);
        } else {
          reject(new Error(`L'équipe '${nomEquipe}' n'a pas été trouvée dans le fichier CSV.`));
        }
      },
      error: (error) => {
        reject(error);
      }
    });
  });
}




function rateGoalEquipe(nomEquipe) {
  return new Promise(async (resolve, reject) => {
    try {
      const equipeLigne = await rechercheLigneEquipe(nomEquipe);

      // Récupération des valeurs nécessaires pour le calcul
      const butsMarques = parseFloat(equipeLigne.BM);
      const matchsJoues = parseFloat(equipeLigne.MJ);
      const butsEncaisses = parseFloat(equipeLigne.BE);
      const totalButs = 380;
      const totalMatchs = 250;

       // Calcul des taux
      const rateGoalA = butsMarques / matchsJoues;
      const rateGoalL = totalButs / totalMatchs;
      // Calcul du potentiel de défense
      const potAI = butsEncaisses / matchsJoues;
      const potAL = potAI / rateGoalL;
      // Force d'attaque powA
      const powA = rateGoalA / rateGoalL ;

      const nbExpA = powA * potAL * rateGoalA;

      resolve(nbExpA);
    } catch (error) {
      reject(error);
    }
    return nbExpA;
  });
}

function poissonEquipe(nbExpA) {
  let maxPMF = 0;
  let maxGoals = 0;

  for (let k = 0; k <= 9; k++) {
    const pmf = Math.exp(-nbExpA) * Math.pow(nbExpA, k) / factorial(k);
    if (pmf > maxPMF) {
      maxPMF = pmf;
      maxGoals = k;
    }
  }

  return maxGoals;
}


function factorial(n) {
  if (n === 0 || n === 1) {
    return 1;
  }
  let result = 1;
  for (let i = 2; i <= n; i++) {
    result *= i;
  }
  return result;
}


function predictionButsEquipes(nbExpA, nbExpB) {
  const butsEquipeA = poissonEquipe(nbExpA);
  const butsEquipeB = poissonEquipe(nbExpB);

  return {
    butsEquipeA, butsEquipeB,
  };
}


// Fonction pour exécuter la prédiction des buts pour les équipes sélectionnées
function runPrediction() {
  const { equipe1, equipe2 } = getEquipe();

  if (equipe1 === equipe2) {
    const output = document.getElementById('output');
    // output.innerHTML = '';
    // const errorResult = document.createElement('p');
    output.textContent = 'Résultat de la prédiction : 0-0';
    // 'Les équipes sélectionnées sont identiques. Les scores sont donc 0-0'
    // output.appendChild(errorResult);
    return;
  }

  // Exécute la prédiction pour l'équipe 1
  rateGoalEquipe(equipe1)
    .then(nbExpA => {
      // Exécute la prédiction pour l'équipe 2
      rateGoalEquipe(equipe2)
        .then(nbExpB => {
          // Prédiction des buts pour les deux équipes
          const { butsEquipeA, butsEquipeB } = predictionButsEquipes(nbExpA, nbExpB);

          // Affichage du score dans l'output
          const output = document.getElementById('output');
          // const scoreResult = document.createElement('p');
          output.textContent = `Résultat de la prédiction : ${butsEquipeA} - ${butsEquipeB}`;
          // `Score dans le match entre ${equipe1} et ${equipe2} : ${butsEquipeA} - ${butsEquipeB}`
          // output.appendChild(scoreResult);
        })
        .catch(error => {
          console.error(error);
        });
    })
    .catch(error => {
      console.error(error);
    });
}

const predictionButton = document.getElementById('predictionButton');
predictionButton.addEventListener('click', runPrediction);
