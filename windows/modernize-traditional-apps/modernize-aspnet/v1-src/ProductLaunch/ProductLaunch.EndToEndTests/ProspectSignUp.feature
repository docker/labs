Feature: Prospect Sign Up
	As a prospect interested in the product launch
	I want to sign up for notifications
	So that I can be updated with news

Scenario Outline: Sign Up with Valid Details
	Given I browse to the Sign Up Page at "localhost:57120"
	And I enter details '<FirstName>' '<LastName>' '<EmailAddress>' '<CompanyName>' '<Country>' '<Role>'
	When I press Go
	Then I should see the Thank You page

Examples:
	| FirstName | LastName | EmailAddress           | CompanyName   | Country        | Role           |
	| Prospect  | A        | a.prospect@company.com | Company, Inc. | United States  | Decision Maker |
	| Prospect  | B        | b.prospect@company.com | Company, Inc. | United Kingdom | Decision Maker |
	| Prospect  | C        | c.prospect@company.com | Company, Inc. | United States  | Architect      |
	| Prospect  | D        | d.prospect@company.com | Company, Inc. | United Kingdom | IT Ops         |
	| Prospect  | E        | e.prospect@company.com | Company, Inc. | United States  | Architect      |
	| Prospect  | F        | f.prospect@other.com   | Other, Inc.   | Sweden         | Decision Maker |
	| Prospect  | G        | g.prospect@company.com | Company, Inc. | United States  | Engineer       |
	| Prospect  | H        | h.prospect@company.com | Company, Inc. | United States  | Architect      |
	| Prospect  | I        | i.prospect@company.com | Company, Inc. | United Kingdom | Decision Maker |
	| Prospect  | J        | j.prospect@company.com | Company, Inc. | United States  | Architect      |
	| Prospect  | K        | k.prospect@other.com   | Other, Inc.   | Sweden         | Decision Maker |
	| Prospect  | L        | l.prospect@company.com | Company, Inc. | United Kingdom | Decision Maker |
	| Prospect  | M        | m.prospect@company.com | Company, Inc. | Sweden         | Architect      |
	| Prospect  | N        | n.prospect@company.com | Company, Inc. | United Kingdom | Decision Maker |
	| Prospect  | O        | o.prospect@company.com | Company, Inc. | United States  | Architect      |
	| Prospect  | P        | p.prospect@other.com   | Other, Inc.   | Sweden         | Decision Maker |
	| Prospect  | Q        | q.prospect@other.com   | Other, Inc.   | Sweden         | Decision Maker |
	| Prospect  | R        | r.prospect@company.com | Company, Inc. | United Kingdom | IT Ops         |
	| Prospect  | S        | s.prospect@company.com | Company, Inc. | United States  | Architect      |
	| Prospect  | T        | t.prospect@company.com | Company, Inc. | United Kingdom | Decision Maker |
	| Prospect  | U        | u.prospect@company.com | Company, Inc. | United States  | Architect      |
	| Prospect  | V        | v.prospect@other.com   | Other, Inc.   | Sweden         | Decision Maker |
	| Prospect  | W        | w.prospect@company.com | Company, Inc. | United States  | Architect      |
	| Prospect  | X        | x.prospect@company.com | Company, Inc. | United Kingdom | Decision Maker |
	| Prospect  | Y        | y.prospect@company.com | Company, Inc. | United States  | Architect      |
	| Prospect  | Z        | z.prospect@other.com   | Other, Inc.   | Sweden         | Decision Maker |