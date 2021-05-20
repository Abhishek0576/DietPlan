import pandas as pd
import sys
import json
import random

ageGroup = sys.argv[1]
wtGroup = sys.argv[2]
cal = float(sys.argv[3])
foodtype = sys.argv[4]

cal -= 175

bfVegCatList = [ 'Dairy products', 'Drinks, Alcohol, Beverages', 'Fruits' , 'Seeds and Nuts', 'Breads, cereals, fastfood,grains' ]
bfNonVegCatList = [ 'Dairy products', 'Eggs', 'Drinks, Alcohol, Beverages', 'Fruits', 'Seeds and Nuts', 'Breads, cereals, fastfood,grains' ]            
lVegCatList = [ 'Breads, cereals, fastfood,grains', 'Soups', 'Dairy products', 'Vegetables' ]               
lNonVegCatList = [ 'Breads, cereals, fastfood,grains', 'Soups', 'Dairy products', 'Meat, Poultry', 'Fish, Seafood', 'Eggs','Vegetables' ]
sVegCatList = [ 'Breads, cereals, fastfood,grains', 'Drinks,Alcohol, Beverages', 'Fruits', 'Desserts, sweets', 'Dairy products', 'Vegetables' ]
sNonVegCatList = [ 'Breads, cereals, fastfood,grains', 'Eggs', 'Drinks,Alcohol, Beverages', 'Fruits', 'Desserts, sweets', 'Dairy products', 'Vegetables' ]
dVegCatList = [ 'Breads, cereals, fastfood,grains', 'Vegetables', 'Desserts, sweets' ]
dNonVegCatList = [ 'Breads, cereals, fastfood,grains', 'Meat, Poultry', 'Vegetables', 'Desserts, sweets' ]


def intersection(lst1, lst2):
    lst3 = [value for value in lst1 if value in lst2]
    return lst3


def printList(lst):
    for item in lst:
        print(item)


def processDiet(table):
    dataframe = pd.read_csv(table)
    
    if foodtype == "veg":
        index1 = dataframe[ dataframe['Category'] == 'Meat, Poultry' ].index
        index2 = dataframe[ dataframe['Category'] == 'Fish, Seafood' ].index
        index3 = dataframe[ dataframe['Category'] == 'Eggs' ].index
        dataframe.drop(index1 , inplace=True)
        dataframe.drop(index2 , inplace=True)
        dataframe.drop(index3 , inplace=True)
        dataframe.to_csv("veg"+table)
        dataframe = pd.read_csv("veg"+table)

    underwtFoods = []
    healthyFoods = []
    overwtFoods  = []
    
    for i in range(0,len(dataframe)):
        if('underweight' in dataframe['Consumed by'][i]):
            underwtFoods.append(dataframe['Food'][i])
        if('healthy' in dataframe['Consumed by'][i]):
            healthyFoods.append(dataframe['Food'][i])
        if('overweight' in dataframe['Consumed by'][i]):
            overwtFoods.append(dataframe['Food'][i])
    
    age_grp1Foods = []
    age_grp2Foods = []
    age_grp3Foods = []
    
    for i in range(0,len(dataframe)):
        if('yes' == dataframe['Age(20-39)'][i]):
            age_grp1Foods.append(dataframe['Food'][i])
        if('yes' == dataframe['Age(40-59)'][i]):
            age_grp2Foods.append(dataframe['Food'][i])
        if('yes' == dataframe['Age(60-more)'][i]):
            age_grp3Foods.append(dataframe['Food'][i])
    
    
    underwt_agegrp1Foods = intersection(underwtFoods,age_grp1Foods)
    healthy_agegrp1Foods = intersection(healthyFoods,age_grp1Foods)
    overwt_agegrp1Foods = intersection(overwtFoods,age_grp1Foods)
    
    underwt_agegrp2Foods = intersection(underwtFoods,age_grp2Foods)
    healthy_agegrp2Foods = intersection(healthyFoods,age_grp2Foods)
    overwt_agegrp2Foods = intersection(overwtFoods,age_grp2Foods)
    
    underwt_agegrp3Foods = intersection(underwtFoods,age_grp3Foods)
    healthy_agegrp3Foods = intersection(healthyFoods,age_grp3Foods)
    overwt_agegrp3Foods = intersection(overwtFoods,age_grp3Foods)

    
    Foodlist = ''
    
    if(ageGroup == "AgeGroup-1(20-39)"):
        if(wtGroup == "Underweight"):
            # print("underwt_agegrp1Foods")
            Foodlist = underwt_agegrp1Foods
        elif(wtGroup == "Normal"): 
            # print("healthy_agegrp1Foods")
            Foodlist = healthy_agegrp1Foods
        elif(wtGroup == "Overweight"):
            # print("overwt_agegrp1Foods")    
            Foodlist = overwt_agegrp1Foods
    
    elif(ageGroup == "AgeGroup-2(40-59)"):
        if(wtGroup == "Underweight"):
            # print("underwt_agegrp2Foods")
            Foodlist = underwt_agegrp2Foods
        elif(wtGroup == "Normal"): 
            # print("healthy_agegrp2Foods")
            Foodlist = healthy_agegrp2Foods
        elif(wtGroup == "Overweight"):
            # print("overwt_agegrp2Foods")
            Foodlist = overwt_agegrp2Foods
    
    else:
        if(wtGroup == "Underweight"):
            # print("underwt_agegrp3Foods")
            Foodlist = underwt_agegrp3Foods
        elif(wtGroup == "Normal"): 
            # print("healthy_agegrp3Foods")
            Foodlist = healthy_agegrp3Foods
        elif(wtGroup == "Overweight"):
            # print("overwt_agegrp3Foods")  
            Foodlist = overwt_agegrp3Foods      
    
    return Foodlist


def getFoodList(table, Foodlist, vCategory, nvCategory, totCal):
    dataframe = pd.read_csv(table)
    session = table[10:-4]
    # print(session)
    
    if foodtype == "veg":
        index1 = dataframe[ dataframe['Category'] == 'Meat, Poultry' ].index
        index2 = dataframe[ dataframe['Category'] == 'Fish, Seafood' ].index
        dataframe.drop(index1 , inplace=True)
        dataframe.drop(index2 , inplace=True)
        dataframe.to_csv("veg"+table)
        dataframe = pd.read_csv("veg"+table)
        category = vCategory
    else:
        category = nvCategory    

    reqCal = 0

    totCal = int(totCal)
    # print("totCal",totCal)
    
    foodDetails = {}

    lst = []
    for i in range(0,len(Foodlist)):
        for j in range(0,len(dataframe["Calories"])):     
            if dataframe["Food"][j] == Foodlist[i]:
                 index = j
                 food = dataframe["Food"][j]
                 cal = dataframe["Calories"][j]
                 fat = dataframe["Fat"][j]
                 protien = dataframe["Protein"][j]
                 qty = dataframe["Measure"][j] 
                 cat = dataframe["Category"][j]
        
        temp = {}
        temp["food"] = food
        temp["cal"] = cal
        temp["fat"] = fat 
        temp["protein"] = protien
        temp["qty"] = qty
        temp["cat"] = cat
        lst.append(temp) 

    foodDetails = lst

    dCat = {}
    for cat in category:
        dCat[cat] = []

    # print(dCat)

    for item in foodDetails:
        # print(item['food'],item['cat'])
        cat = item['cat']
        if cat in category:
            flist = dCat[item['cat']]
            flist.append(item['food']) 
            dCat[item['cat']] = flist 

    # print(dCat)
    
    sessionFood = []

    for catName in dCat:
        catList = dCat[catName]
        if len(catList) != 0:
            sessionFood.append(random.choice(catList))

    # print(sessionFood,end="\n\n")            

    
    sessionDetails = []

    for item in foodDetails:
        for foodItem in sessionFood:
            if(item['food'] == foodItem):
                foodCal = int(item['cal'])
                if(reqCal + foodCal <= totCal):
                    sessionDetails.append(item)
                    reqCal = reqCal + foodCal 
                    # print(reqCal)
                else:
                    break    

    # print()
    # print(sessionDetails)          

    return sessionDetails
    # return foodDetails


breakfast_csv = 'nutrients_breakfast.csv'
lunch_csv     = 'nutrients_lunch.csv'
snack_csv     = 'nutrients_snack.csv'
dinner_csv    = 'nutrients_dinner.csv'

breakfastFoods = processDiet(breakfast_csv)
lunchFoods = processDiet(lunch_csv)
snackFoods = processDiet(snack_csv)
dinnerFoods = processDiet(dinner_csv)

breakfastList = getFoodList(breakfast_csv, breakfastFoods, bfVegCatList, bfNonVegCatList, int(cal/4))
lunchList = getFoodList(lunch_csv, lunchFoods, lVegCatList, lNonVegCatList, int(cal/4))
snackList = getFoodList(snack_csv, snackFoods, sVegCatList, sNonVegCatList, 175)
dinnerList = getFoodList(dinner_csv, dinnerFoods, dVegCatList, dNonVegCatList, int(cal/4))


dietplan = { "breakfast" : breakfastList, 
             "lunch" : lunchList, 
             "snack" : snackList, 
             "dinner" : dinnerList }

print(dietplan) 