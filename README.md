lastpictures
============

L'estensione lastpictures è l'unione di 2 vecchie MOD create da Micogian (Giovanni Dose), LastPictures e TopTenTopics.
L'estensione comprende 4 opzioni:
1) visualizza una lista fotografica degli ultimi allegati
2) visualizza la lista degli ultimi argomenti (topics)
3) visualizza la lista delle ultime risposte (posts)
4) visualizza la lista dei topics più visti.

a) Installare l'estensione nel percorso ext/micogian/lastpictures/
b) Aprire con un progranna di testo (notepad++ o similare)  il file ext/micogian/pictures/includes/lastpictures_var.php 
e modificare l'elenco dei forum_id da elaborare. Ogni lista prevede una variabile, normalmente l'elenco dei forum da elaborare è lo stesso per tutte le opzioni ma potrebbe essere necessario creare elenchi diversi in base alla struttura del Furum, ad esempio nel caso che uno volesse visualizzare le immagini solo di alcuni Forum specifici.
ATTENZIONE = le immagini allegate vengono elaborate solo se vengono allegate nel primo post, le immagini delle pagine successive non vengono inserite nella Lista, il motivo è semplice: la striscia fotografica vuol essere un indice figurato degli ultimi topics, non ha senso visualizzare immagini dello stesso topics, pertanto se l'immagine non è stata inserita nel primo post non viene visualizzata.
