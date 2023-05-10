# Shortcode pour Atmo Auvergne-Rhône-Alpes

Auteur [Arthur Bazin](https://www.arthurbazin.com)

Plugin WordPress pour récupérer et afficher les données de l'API Atmo Auvergne-Rhône-Alpes.  
:warning: Ce plugin est NON officiel.

Pour récupérer les données, une clé d'API est nécessaire et disponible depuis [le site de l'API](https://api.atmo-aura.fr/documentation).


## Documentation

L'utilisation se fait simplement en plaçant le shortcode suivant : ```[spaa]```

Plusieurs paramètres sont disponibles afin de spécifier les données voulues :
- `indicateur` :
  - `horodatage` : renvoi la date et l'heure de récupération des données.
  - `lien` : renvoi un lien vers les données de la commune dont le code INSEE est spécifiée dans les paramètres. A utiliser avec le paramètre : `texte`,
  - `vigilance` : renvoi un bloc HTML détaillant les vigilances pollution. Les balises utilisées sont `<p>` s'il n'y a aucune vigilance, `<ul>` s'il y a une ou plusieurs vigilances.
  - `recommandation` : renvoi un bloc HTML détaillant les recommandations en fonction de l'indice global actuel.
  - `indice` : defaut : renvoi différentes informations sur l'état de l'atmosphère. A utiliser avec les paramètres : `echeance`, `polluant` et `parametre`.
- `echeance` : uniquement avec le paramètre `indicateur=indice`.
  - `n` : défaut : valeurs de pollution pour le jour même.
  - `n+1` : valeurs de pollution pour le jour suivant.
- `polluant` : polluant pour lequel afficher les données, uniquement avec le paramètre `indicateur=indice`.
  - `global` : défaut : état global de l'air.
  - `PM2.5` : microparticule inférieur à 2,5 micron.
  - `PM10` : microparticule inférieur à 10 micron.
  - `SO2` : dioxyde de souffre.
  - `O3` : ozone.
  - `NO2` : dioxyde d'azote.
- `parametre` : uniquement avec le paramètre `indicateur=indice`.
  - `nom` : nom du polluant (par exemple "Ozone").
  - `abbreviation` : abbreviation (par exemple "03").
  - `indice num` : indice numérique (par exemple "3").
  - `indice txt` : indice textuel (par exemple "Mauvais").
  - `concentration` : valeur de concentration absolue.
  - `image` : icône colorée représentant l'indice.
  - `widget` : defaut : code HTML contenant un ensemble d'informations sur le polluant et l'indice lié.
  - `gauge` : code HTML contenant une gauge indicant le niveau d'indice pour le polluant considéré.
- `texte` : uniquement avec le paramètre `indicateur=lien`. Texte à afficher dans le lien généré. Par défaut, le texte suivant est utilisé : "Données de ma commune sur le site de l'observatoire de la qualité de l'air en Auvergne-Rhône-Alpes"
- `debug` : utilisé sans valeur, les données bruttes sont renvoyées. Ce paramètre prime sur tous les autres.

Voici quelques exemples d'utilisation du shortcode :  
```[spaa]```  
=> equivalent à ```[spaa indicateur="indice" echeance="n" polluant="global" parametre="widget"]```  

```[spaa echeance="n+1" polluant="PM2.5"]```  
=>equivalent à ```[spaa indicateur="indice" echeance="n+1" polluant="PM2.5" parametre="widget"]```  

```[spaa echeance="n+1" polluant="PM2.5" debug]```  
=>equivalent à ```[spaa debug]```  

```[spaa indicateur="vigilance"]```  

```[spaa indicateur="horodatage"]```  

```[spaa indicateur="recommandation"]```  

```[spaa indicateur="lien" texte="Données pour ma commune"]``` 


## Quelques captures d'écran

![Ecran de paramétrage](/doc/screenshot-1.png)
![Exemple de rendu](/doc/screenshot-2.png)
![Exemple de mise en forme pour le rendu de la capture précédente](/doc/screenshot-3.png)

