<?php

class qa_html_theme_layer extends qa_html_theme_base
{

	function ranking($ranking) 
	{
		
		if($this->template=='users') 
		{
			$userrank = 1 + qa_get_start(); // if requested others
			
			if(@$ranking['type']=='users') 
			{
				foreach($ranking['items'] as $idx => $item) 
				{
					$handle = preg_replace('/ *<[^>]+> */', '', $item['label']);
					
					if(isset($ranking['items'][$idx]['score'])) 
					{
						$ranking['items'][$idx]['score'] .= '</td><td class="qa-users-acceptrate-cell">'.qa_get_acceptance_rate_A($handle);
						$userid = qa_db_read_one_value( qa_db_query_sub('SELECT userid FROM ^users WHERE handle = $',$handle), true );
						if(isset($userid)) 
						{
							$ranking['items'][$idx]['score'] .= '</td><td class="qa-users-upvotes-cell">'.qa_get_the_user_upvotes($userid);
							$ranking['items'][$idx]['score'] .= '</td><td class="qa-users-answercount-cell">'.qa_get_the_user_acount($userid);
							// number users, add number in front
							$ranking['items'][$idx]['label'] = '<span class="userrank">'.$userrank++.'</span> </td><td class="qa-top-users-label">'.$ranking['items'][$idx]['label'];
						}
					}
				}
			}
		} // end template users
		
		qa_html_theme_base::ranking($ranking);
	}

}
