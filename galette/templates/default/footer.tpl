        <footer>
		{_T string="CNIL"}</br>
            <a id="copyright" href="http://galette.eu/">Galette {$GALETTE_VERSION}</a>
{if $login->isLogged() &&  ($login->isAdmin() or $login->isStaff())}
            <br/><a id="sysinfos" href="{$galette_base_path}sysinfos.php">{_T string="System informations"}</a>
{/if}
            <nav>
                <ul>
                    <li><strong>Procedures</strong></li>
                    <li><a href="{$galette_base_path}{$subscription_dir}download/procedure_inscription_AS_Nexter_2014_B.pdf" target="blank">Procedure_inscription</a></li>
                    <li><a href="{$galette_base_path}{$subscription_dir}download/procedure_responsable_2014_C.pdf" target="blank">Procedure_responsable_de_section</a></li>
                    
                </ul>
               
				<ul>
                    <li><strong>{_T string="The project: "}</strong></li>
                    <li><a href="http://galette.eu">{_T string="Website"}</a></li>
                    <li><a href="http://galette.eu/documentation/">{_T string="Documentation"}</a></li>
					<li><a href="{$galette_base_path}{$subscription_dir}download/AS_Nexter_presentation_galette_indB.pptx" target="blank">Presentation_de_Galette</a></li>
                </ul>
                <ul>
                    <li>
                        <a href="https://twitter.com/galette_soft" class="twitter-galette-button"><img src="{$template_subdir}images/twitter.png" alt="{_T string="%s on Twitter!" pattern="/%s/" replace="@galette_soft"}"/></a>
                    </li>
                    <li>
                        <a href="https://plus.google.com/116977415489200387309"><img src="{$template_subdir}images/gplus.png" alt="{_T string="%s on Google+!" pattern="/%s/" replace="Galette"}"/></a>
                    </li>
					
                    
                </ul>
            </nav>
        </footer>

