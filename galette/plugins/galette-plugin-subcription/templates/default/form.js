function reponse(form1) 
{ 
var result = 0 ;
var total=0;

function virgule(texte) {
   while(texte.indexOf(',')>-1){
    texte=texte.replace(",",".");
	}
 
     return texte
}

for (var i=0; i<form1.length;i++)
	{
		if (form1[i].checked)
		{
		result = result+virgule(form1[i].value)*1 ;
		}
	}
document.form1.total.value=result;
} 