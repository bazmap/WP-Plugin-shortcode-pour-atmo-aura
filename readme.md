# Shortcode pour Atmo Auvergne-Rhône-Alpes

Auteur [Arthur Bazin](https://www.arthurbazin.com)

Plugin WordPress pour récupérer et afficher les données de l'API Atmo Auvergne-Rhône-Alpes.  
:warning: Ce plugin est NON officiel.

Pour récupérer les données, une clé d'API est nécessaire et disponible depuis [le site de l'API](https://api.atmo-aura.fr/documentation).

L'utilisation se fait simplement en plaçant le shortcode suivant :
```[spaa]```

Plusieurs paramètres sont disponibles afin de spécifier les données voulues :
- `indicateur` : "vigilance" ou "indice".
  - `vigilance` : renvoi un bloc HTML détaillant les vigilances pollution. Les balises utilisées sont `<p>` s'il n'y a aucune vigilance, `<ul>` s'il y a une ou plusieurs vigilances.
  - `indice` : defaut : indice de pollution. A utiliser avec les paramètres suivants : `echeance` et `parametre`.
- `echeance` : uniquement avec le paramètre `indicateur=indice`.
  - `n` : defaut : valeurs de pollution pour le jour même.
  - `n+1` : valeurs de pollution pour le jour suivant.
- `parametre` : uniquement avec le paramètre `indicateur=indice`.
  - `global_valeur` : Valeur du paramètre.
  - `global_indice` : defaut : Indice.
  - `global_couleur` : Code couleur hexadecimal.
  - `PM2.5` : Code HTML contenant les données de pollution au microparticule inférieur à 2,5 micron.
  - `PM10` : Code HTML contenant les données de pollution au microparticule inférieur à 10 micron.
  - `SO2` : Code HTML contenant les données de pollution au dioxyde de souffre.
  - `O3` : Code HTML contenant les données de pollution à l'ozone.
  - `NO2` : Code HTML contenant les donnée de pollution au dioxyde d'azote.
- `debug` : utilisé sans valeur, les données bruttes sont renvoyées. Ce paramètre prime sur tous les autres.

Voici quelques exemples d'utilisation du shortcode :  
```[spaa]```  
=> equivalent à ```[spaa indicateur="indice" echeance="n" parametre="global_indice"]```  

```[spaa echeance="n+1" parametre="PM2.5"]```  
=>equivalent à ```[spaa indicateur="indice" echeance="n+1" parametre="PM2.5"]```  

```[spaa echeance="n+1" parametre="PM2.5" debug]```  
=>equivalent à ```[spaa debug]```  

```[spaa indicateur="vigilance"]```  


