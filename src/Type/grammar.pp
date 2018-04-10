// Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

%skip whitespace \s+

%token parenthesis_ <
%token _parenthesis >
%token comma        ,
%token name         (?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*

%token quote_                      "            -> quoted_string
%token quoted_string:quoted_string (?:[^"]|"")+
%token quoted_string:_quote        "            -> default

%token apostrophe_                           '            -> apostrophed_string
%token apostrophed_string:apostrophed_string (?:[^']|'')+
%token apostrophed_string:_apostrophe        '            -> default

type:
    simple_type() | compound_type()

#simple_type:
    <name>
    | ::quote_:: <quoted_string> ::_quote::
    | ::apostrophe_:: <apostrophed_string> ::_apostrophe::

#compound_type:
    <name>
    ::parenthesis_::
    type()
    ( ::comma:: type() )*
    ::_parenthesis::
