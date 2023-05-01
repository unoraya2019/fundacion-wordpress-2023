# Basic Commands

build:
	docker build -t unoraya/wp -f compose/Dockerfile.prod .
	docker tag unoraya/wp 761265910279.dkr.ecr.us-east-1.amazonaws.com/fundacionbolivar:latest
	docker push 761265910279.dkr.ecr.us-east-1.amazonaws.com/fundacionbolivar:latest

