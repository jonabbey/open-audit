<?php

$query_array=array("headline"=>"All IE Browser-Helper-Objects",
                   "sql"=>"SELECT count(bho_program_file) as bho_count, bho_program_file, bho_status, bho_code_base from browser_helper_objects GROUP BY bho_program_file, bho_status ",
                   "sort"=>"bho_program_file",
                   "dir"=>"ASC",
                   "get"=>array("file"=>"list.php",
                                "title"=>"Systems installed this Software",
                                "var"=>array("view"=>"systems_for_bho",
                                             "name"=>"%bho_program_file",
                                             "headline_addition"=>"%bho_program_file",
                                            ),
                               ),
                   "fields"=>array("10"=>array("name"=>"bho_count",
                                               "head"=>"Count",
                                               "show"=>"y",
                                               "link"=>"y",
                                               "sort"=>"n",
                                               "search"=>"n",
                                              ),
                                   "20"=>array("name"=>"bho_program_file",
                                               "head"=>"Name",
                                               "show"=>"y",
                                               "link"=>"y",
                                              ),
                                   "30"=>array("name"=>"bho_status",
                                               "head"=>"Status",
                                               "show"=>"y",
                                               "sort"=>"n",
                                               "help"=>"%bho_code_base",
                                              ),

                                   "40"=>array("name"=>"Google",
                                               "head"=>"Google",
                                               "image"=>"images/button_google.gif",
                                               "align"=>"center",
                                               "show"=>"y",
                                               "link"=>"y",
                                               "sort"=>"n",
                                               "search"=>"n",
                                               "get"=>array("file"=>"http://www.google.de/search",
                                                            "title"=>"External Link",
                                                            "var"=>array("q"=>"%bho_program_file",
                                                                        ),
                                                            "target"=>"_BLANK",
                                                           ),
                                              ),
                                  ),
                  );
?>
